<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\Response;
use App\Validators\LessonGetValidator;
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

    /** @var LessonGetValidator */
    private $lessonGetValidator;

    /** @var LessonSearchValidator */
    private $lessonSearchValidator;

    /**
     * @param ResponseHelper        $responseHelper
     * @param LessonGetValidator    $lessonGetValidator
     * @param LessonSearchValidator $lessonSearchValidator
     */
    public function __construct(
        ResponseHelper $responseHelper,
        LessonGetValidator $lessonGetValidator,
        LessonSearchValidator $lessonSearchValidator
    ) {
        $this->responseHelper = $responseHelper;
        $this->lessonGetValidator = $lessonGetValidator;
        $this->lessonSearchValidator = $lessonSearchValidator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionGet(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->lessonGetValidator->validate($data);

            return new Response($data);
        }, [$request]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionSearch(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->lessonSearchValidator->validate($data);

            return new Response($data);
        }, [$request]);
    }
}
