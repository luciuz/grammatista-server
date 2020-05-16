<?php

namespace App\Api\Controllers;

use App\Api\Dtos\IdDto;
use App\Api\Dtos\VariantDto;
use App\Api\Dtos\VariantFinishDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\BadRequestResponse;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\NotFoundResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\DataAssemblers\VariantDataAssembler;
use App\Repositories\TestRepository;
use App\Repositories\VariantRepository;
use App\Services\Idempotent\IdempotentMutexException;
use App\Exceptions\IdempotentException;
use App\Services\Idempotent\IdempotentService;
use App\Services\VariantService;
use App\Validators\GetValidator;
use App\Validators\ListValidator;
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
    private const MAX_ERRORS = 0;

    /** @var ResponseHelper */
    private $responseHelper;

    /** @var GetValidator */
    private $variantGetValidator;

    /** @var VariantCreateValidator */
    private $variantCreateValidator;

    /** @var VariantFinishValidator */
    private $variantFinishValidator;

    /** @var ListValidator */
    private $variantListValidator;

    /** @var VariantService */
    private $variantService;

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
     * @param ListValidator          $variantListValidator
     * @param VariantService         $variantService
     * @param TestRepository         $testRepository
     * @param VariantDataAssembler   $variantDataAssembler
     * @param IdempotentService      $idempotentService
     */
    public function __construct(
        ResponseHelper $responseHelper,
        GetValidator $variantGetValidator,
        VariantCreateValidator $variantCreateValidator,
        VariantFinishValidator $variantFinishValidator,
        ListValidator $variantListValidator,
        VariantService $variantService,
        TestRepository $testRepository,
        VariantDataAssembler $variantDataAssembler,
        IdempotentService $idempotentService
    ) {
        $this->responseHelper = $responseHelper;
        $this->variantGetValidator = $variantGetValidator;
        $this->variantCreateValidator = $variantCreateValidator;
        $this->variantFinishValidator = $variantFinishValidator;
        $this->variantListValidator = $variantListValidator;
        $this->variantService = $variantService;
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

            $lessonId = $data['lessonId'];
            $userId = \Auth::user()->getAuthIdentifier();
            try {
                $result = $this->idempotentService
                    ->runIdempotent($data['transactionToken'], [$this, 'create'], [$lessonId, $userId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            } catch (IdempotentException $e) {
                return new NotFoundResponse($e->getMessage());
            }
        }, [$request]);
    }

    /**
     * @see VariantDto
     * @param Request $request
     * @return Response
     */
    public function actionGet(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantGetValidator->validate($data);

            $userId = \Auth::user()->getAuthIdentifier();
            $variant = $this->variantService->getRepository()->findRichById($data['id'], $userId);
            if ($variant === null) {
                return new NotFoundResponse();
            }

            $result = $this->variantDataAssembler->make($variant);
            return new Response($result);
        }, [$request]);
    }

    /**
     * @see VariantFinishDto
     * @param Request $request
     * @return Response
     */
    public function actionFinish(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantFinishValidator->validate($data);

            $userId = \Auth::user()->getAuthIdentifier();
            try {
                $result = $this->idempotentService
                    ->runIdempotent($data['transactionToken'], [$this, 'finish'], [$data, $userId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            } catch (IdempotentException $e) {
                switch ($e->getCode()) {
                    case 404:
                        return new NotFoundResponse($e->getMessage());
                    case 403:
                        return new ForbiddenResponse($e->getMessage());
                    case 400:
                    default:
                        return new BadRequestResponse($e->getMessage());
                }
            }
        }, [$request]);
    }

    /**
     * @see ResultListDto
     * @param Request $request
     * @return Response
     */
    public function actionList(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->variantListValidator->validate($data);

            $result = $this->list($data);
            return new Response($result);
        }, [$request]);
    }

    /**
     * @param int $lessonId
     * @param int $userId
     * @return array|null
     * @throws IdempotentException
     */
    public function create(int $lessonId, int $userId): ?array
    {
        $test = $this->testRepository->findByLessonId($lessonId);
        if ($test === null) {
            throw new IdempotentException('Test not found.', 404);
        }

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

        $variant = $this->variantService->getRepository()->create($params);
        return ['id' => $variant->id];
    }

    /**
     * @param array $data
     * @param int   $userId
     * @return array|null
     * @throws IdempotentException
     */
    public function finish(array $data, int $userId): ?array
    {
        $repository = $this->variantService->getRepository();
        $variant = $repository->findById($data['id']);
        if ($variant === null) {
            throw new IdempotentException('Variant not found.', 404);
        }
        if ($variant->user_id !== $userId) {
            throw new IdempotentException(null, 403);
        }

        $answerList = $variant->answer['list'];
        $userAnswer = $data['userAnswer'];
        if (count($answerList) !== count($userAnswer['list'])) {
            throw new IdempotentException('Invalid user answer count.', 400);
        }

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
        $isComplete = ($errors <= self::MAX_ERRORS);
        $params = [
            'is_complete' => $isComplete,
            'user_answer' => $userAnswer,
            'result' => [
                'list' => $result
            ],
            'finished_at' => Carbon::now(),
        ];

        $repository->update($variant, $params);
        return compact('isComplete');
    }

    /**
     * @param array $data
     * @return array
     */
    private function list(array $data): array
    {
        return $this->variantService->list(
            $data['maxId'] ?? null,
            \Auth::user()->getAuthIdentifier()
        );
    }
}
