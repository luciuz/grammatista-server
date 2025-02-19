<?php

namespace App\Api\Controllers;

use App\Api\Dtos\LessonSearchDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\NotFoundResponse;
use App\Api\Responses\Response;
use App\Services\LessonService;
use App\Validators\GetValidator;
use App\Validators\LessonSearchValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class LessonController
 * @package App\Api\Controllers
 */
class LessonController extends BaseController
{
    /** @var ResponseHelper */
    private $responseHelper;

    /** @var GetValidator */
    private $lessonGetValidator;

    /** @var LessonSearchValidator */
    private $lessonSearchValidator;

    /** @var LessonService */
    private $lessonService;

    /**
     * @param ResponseHelper        $responseHelper
     * @param GetValidator          $lessonGetValidator
     * @param LessonSearchValidator $lessonSearchValidator
     * @param LessonService         $lessonService
     */
    public function __construct(
        ResponseHelper $responseHelper,
        GetValidator $lessonGetValidator,
        LessonSearchValidator $lessonSearchValidator,
        LessonService $lessonService
    ) {
        $this->responseHelper = $responseHelper;
        $this->lessonGetValidator = $lessonGetValidator;
        $this->lessonSearchValidator = $lessonSearchValidator;
        $this->lessonService = $lessonService;
    }

    /**
     * @see LessonRichDto
     * @param Request $request
     * @return Response
     */
    public function actionGet(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->lessonGetValidator->validate($data);

            $result = $this->lessonService->getRichById($data['id'], \Auth::user()->getAuthIdentifier());
            if ($result === null) {
                return new NotFoundResponse();
            }

            return new Response($result);
        }, [$request]);
    }

    /**
     * @see LessonSearchDto
     * @param Request $request
     * @return Response
     */
    public function actionSearch(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->lessonSearchValidator->validate($data);

            $result = $this->search($data);
            return new Response($result);
        }, [$request]);
    }

    /**
     * @param array $data
     * @return array
     */
    public function search(array $data): array
    {
        return $this->lessonService->search(
            $data['q'],
            $data['maxId'] ?? null,
            \Auth::user()->getAuthIdentifier()
        );
    }
}
