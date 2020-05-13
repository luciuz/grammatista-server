<?php

namespace App\Api\Controllers;

use App\Api\Dtos\IdDto;
use App\Api\Dtos\VariantDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\BadRequestResponse;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\NotFoundResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\DataAssemblers\VariantDataAssembler;
use App\Models\Test;
use App\Models\Variant;
use App\Repositories\TestRepository;
use App\Repositories\VariantRepository;
use App\Services\Idempotent\IdempotentMutexException;
use App\Services\Idempotent\IdempotentService;
use App\Validators\GetValidator;
use App\Validators\VariantCreateValidator;
use App\Validators\VariantFinishValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class VariantController
 * @package App\Api\Controllers
 */
class VariantController extends BaseController
{
    private const MAX_ERRORS = 2;

    /** @var ResponseHelper */
    private $responseHelper;

    /** @var GetValidator */
    private $variantGetValidator;

    /** @var VariantCreateValidator */
    private $variantCreateValidator;

    /** @var VariantFinishValidator */
    private $variantFinishValidator;

    /** @var VariantRepository */
    private $variantRepository;

    /** @var TestRepository */
    private $testRepository;

    /** @var VariantDataAssembler */
    private $variantDataAssembler;

    /** @var IdempotentService */
    private $idempotentService;

    /**
     * @param ResponseHelper         $responseHelper
     * @param GetValidator           $variantGetValidator
     * @param VariantCreateValidator $variantCreateValidator
     * @param VariantFinishValidator $variantFinishValidator
     * @param VariantRepository      $variantRepository
     * @param TestRepository         $testRepository
     * @param VariantDataAssembler   $variantDataAssembler
     * @param IdempotentService      $idempotentService
     */
    public function __construct(
        ResponseHelper $responseHelper,
        GetValidator $variantGetValidator,
        VariantCreateValidator $variantCreateValidator,
        VariantFinishValidator $variantFinishValidator,
        VariantRepository $variantRepository,
        TestRepository $testRepository,
        VariantDataAssembler $variantDataAssembler,
        IdempotentService $idempotentService
    ) {
        $this->responseHelper = $responseHelper;
        $this->variantGetValidator = $variantGetValidator;
        $this->variantCreateValidator = $variantCreateValidator;
        $this->variantFinishValidator = $variantFinishValidator;
        $this->variantRepository = $variantRepository;
        $this->testRepository = $testRepository;
        $this->variantDataAssembler = $variantDataAssembler;
        $this->idempotentService = $idempotentService;
    }

    /**
     * @param Request $request
     * @return Response
     * @see IdDto
     */
    public function actionCreate(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantCreateValidator->validate($data);

            $test = $this->testRepository->findByLessonId($data['lessonId']);
            if ($test === null) {
                return new NotFoundResponse('Test not found.');
            }

            $userId = \Auth::user()->getAuthIdentifier();
            try {
                $result = $this->idempotentService
                    ->runIdempotent($data['transaction_token'], [$this, 'create'], [$test, $userId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            }
        }, [$request]);
    }

    /**
     * @param Request $request
     * @return Response
     * @see VariantDto
     */
    public function actionGet(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantGetValidator->validate($data);

            $variant = $this->variantRepository->findById($data['id']);
            if ($variant === null) {
                return new NotFoundResponse();
            }
            if ($variant->user_id !== \Auth::user()->getAuthIdentifier()) {
                return new ForbiddenResponse();
            }

            $result = $this->variantDataAssembler->make($variant->getAttributes());
            return new Response($result);
        }, [$request]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionFinish(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantFinishValidator->validate($data);

            $variant = $this->variantRepository->findById($data['id']);
            if ($variant === null) {
                return new NotFoundResponse();
            }
            if ($variant->user_id !== \Auth::user()->getAuthIdentifier()) {
                return new ForbiddenResponse();
            }

            $answerList = $variant->answer['list'];
            $userAnswer = $data['userAnswer'];
            if (count($answerList) !== count($userAnswer['list'])) {
                return new BadRequestResponse('Invalid user answer count.');
            }

            try {
                $result = $this->idempotentService->runIdempotent(
                    $data['transaction_token'],
                    [$this, 'finish'],
                    [$variant, $answerList, $userAnswer]
                );
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            }
        }, [$request]);
    }

    /**
     * @param Test $test
     * @param int  $userId
     * @return array|null
     */
    public function create(Test $test, int $userId): ?array
    {
        $params = [
            'is_complete' => false,
            'lesson_id'   => $test->lesson_id,
            'test_id'     => $test->id,
            'user_id'     => $userId,
            'question'    => $test->question,
            'answer'      => $test->answer,
        ];

        if ($test->duration) {
            $params['expired_at'] = Carbon::now()->modify($test->duration . ' seconds');
        }

        $variant = $this->variantRepository->create($params);
        return ['id' => $variant->id];
    }

    /**
     * @param Variant $variant
     * @param array   $answerList
     * @param array   $userAnswer
     * @return array|null
     */
    public function finish(Variant $variant, array $answerList, array $userAnswer): ?array
    {
        $result = [];
        $errors = 0;
        $userAnswerList = $userAnswer['list'];
        foreach ($answerList as $answerListItem) {
            sort($answerListItem);
            $userAnswerItem = current($userAnswerList);
            sort($userAnswerItem);
            if ($answerListItem === $userAnswerItem) {
                $result[] = true;
            } else {
                $result[] = false;
                $errors++;
            }
            next($userAnswerList);
        }
        $params = [
            'is_complete' => ($errors <= self::MAX_ERRORS),
            'user_answer' => $userAnswer,
            'result' => [
                'list' => $result
            ],
            'finished_at' => Carbon::now(),
        ];

        $this->variantRepository->update($variant, $params);
        return null;
    }
}
