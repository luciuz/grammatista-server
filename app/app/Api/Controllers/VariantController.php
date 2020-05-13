<?php

namespace App\Api\Controllers;

use App\Api\Dtos\IdDto;
use App\Api\Dtos\VariantDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\NotFoundResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\DataAssemblers\VariantDataAssembler;
use App\Models\Test;
use App\Repositories\TestRepository;
use App\Repositories\VariantRepository;
use App\Services\Idempotent\IdempotentMutexException;
use App\Services\Idempotent\IdempotentService;
use App\Validators\GetValidator;
use App\Validators\VariantCreateValidator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class VariantController
 * @package App\Api\Controllers
 */
class VariantController extends BaseController
{
    /** @var ResponseHelper */
    private $responseHelper;

    /** @var GetValidator */
    private $variantGetValidator;

    /** @var VariantCreateValidator */
    private $variantCreateValidator;

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
     * @param VariantRepository      $variantRepository
     * @param TestRepository         $testRepository
     * @param VariantDataAssembler   $variantDataAssembler
     * @param IdempotentService      $idempotentService
     */
    public function __construct(
        ResponseHelper $responseHelper,
        GetValidator $variantGetValidator,
        VariantCreateValidator $variantCreateValidator,
        VariantRepository $variantRepository,
        TestRepository $testRepository,
        VariantDataAssembler $variantDataAssembler,
        IdempotentService $idempotentService
    ) {
        $this->responseHelper = $responseHelper;
        $this->variantGetValidator = $variantGetValidator;
        $this->variantCreateValidator = $variantCreateValidator;
        $this->variantRepository = $variantRepository;
        $this->testRepository = $testRepository;
        $this->variantDataAssembler = $variantDataAssembler;
        $this->idempotentService = $idempotentService;
    }

    /**
     * @see IdDto
     * @param Request $request
     * @return Response
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

            try {
                $userId = \Auth::user()->getAuthIdentifier();
                $result = $this->idempotentService
                    ->runIdempotent($data['transaction_token'], [$this, 'create'], [$test, $userId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
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
}
