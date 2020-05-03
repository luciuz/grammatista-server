<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\Response;
use App\Validators\UserAuthValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class UserController
 * @package App\Api\Controllers
 */
class UserController extends BaseController
{
    /** @var ResponseHelper */
    private $responseHelper;

    /** @var UserAuthValidator */
    private $userAuthValidator;

    /**
     * @param ResponseHelper    $responseHelper
     * @param UserAuthValidator $userAuthValidator
     */
    public function __construct(
        ResponseHelper $responseHelper,
        UserAuthValidator $userAuthValidator
    ) {
        $this->responseHelper = $responseHelper;
        $this->userAuthValidator = $userAuthValidator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionAuth(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->query->all();
            $this->userAuthValidator->validate($data);

            $token = '123123';
            $view = null;
            return new Response(compact('token', 'view'));
        }, [$request]);
    }
}
