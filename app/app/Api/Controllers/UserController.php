<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\Response;
use App\Repositories\UserRepository;
use App\Services\SessionService;
use App\Validators\UserAuthValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class UserController
 * @package App\Api\Controllers
 */
class UserController extends BaseController
{
    /** @var UserRepository */
    private $userRepository;

    /** @var SessionService */
    private $sessionService;

    /** @var ResponseHelper */
    private $responseHelper;

    /** @var UserAuthValidator */
    private $userAuthValidator;

    /**
     * @param UserRepository    $userRepository
     * @param SessionService    $sessionService
     * @param ResponseHelper    $responseHelper
     * @param UserAuthValidator $userAuthValidator
     */
    public function __construct(
        UserRepository $userRepository,
        SessionService $sessionService,
        ResponseHelper $responseHelper,
        UserAuthValidator $userAuthValidator
    ) {
        $this->userRepository = $userRepository;
        $this->sessionService = $sessionService;
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

            $view = null;
            $token = $this->sessionService->generateToken();

            $user = $this->userRepository->findByVkId($data['vk_user_id']);
            if (!$user) {
                $user = $this->userRepository->create([
                    'is_active' => true,
                    'vk_id'     => $data['vk_user_id'],
                ]);
                $view = 'welcome';
            } elseif ($user->is_active === false) {
                return new ForbiddenResponse();
            }

            $data['app_user_id'] = $user->id;
            $this->sessionService->createSession($token, $data);

            return new Response(compact('token', 'view'));
        }, [$request]);
    }
}
