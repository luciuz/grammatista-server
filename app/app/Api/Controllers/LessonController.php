<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\Response;
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

    /** @var LessonSearchValidator */
    private $lessonSearchValidator;

    /**
     * @param ResponseHelper        $responseHelper
     * @param LessonSearchValidator $lessonSearchValidator
     */
    public function __construct(
        ResponseHelper $responseHelper,
        LessonSearchValidator $lessonSearchValidator
    ) {
        $this->responseHelper = $responseHelper;
        $this->lessonSearchValidator = $lessonSearchValidator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionSearch(Request $request): Response
    {
        return $this->responseHelper->run(function () use ($request) {
            $data = $request->query->all();
            $this->lessonSearchValidator->validate($data);

            return new Response($data);
        });
    }
}
