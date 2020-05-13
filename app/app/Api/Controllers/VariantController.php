<?php

namespace App\Api\Controllers;

use App\Api\Dtos\IdDto;
use App\Api\Dtos\VariantDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\NotFoundResponse;
use App\Api\Responses\Response;
use App\DataAssemblers\VariantDataAssembler;
use App\Repositories\VariantRepository;
use App\Validators\GetValidator;
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

    /** @var VariantRepository */
    private $variantRepository;

    /** @var VariantDataAssembler */
    private $variantDataAssembler;

    /**
     * @param ResponseHelper       $responseHelper
     * @param GetValidator         $variantGetValidator
     * @param VariantRepository    $variantRepository
     * @param VariantDataAssembler $variantDataAssembler
     */
    public function __construct(
        ResponseHelper $responseHelper,
        GetValidator $variantGetValidator,
        VariantRepository $variantRepository,
        VariantDataAssembler $variantDataAssembler
    ) {
        $this->responseHelper = $responseHelper;
        $this->variantGetValidator = $variantGetValidator;
        $this->variantRepository = $variantRepository;
        $this->variantDataAssembler = $variantDataAssembler;
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
            $this->variantGetValidator->validate($data);

            $result = $this->variantRepository->findById($data['id']);
            if ($result === null) {
                return new NotFoundResponse();
            }

            return new Response($result);
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
}
