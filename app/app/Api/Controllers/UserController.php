<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\Response;
use App\Repositories\UserRepository;
use App\Repositories\UserSessionRepository;
use App\Validators\UserAuthValidator;
use Carbon\Carbon;
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

    /** @var UserSessionRepository */
    private $userSessionRepository;

    /** @var ResponseHelper */
    private $responseHelper;

    /** @var UserAuthValidator */
    private $userAuthValidator;

    /**
     * @param UserRepository        $userRepository
     * @param UserSessionRepository $userSessionRepository
     * @param ResponseHelper        $responseHelper
     * @param UserAuthValidator     $userAuthValidator
     */
    public function __construct(
        UserRepository $userRepository,
        UserSessionRepository $userSessionRepository,
        ResponseHelper $responseHelper,
        UserAuthValidator $userAuthValidator
    ) {
        $this->userRepository = $userRepository;
        $this->userSessionRepository = $userSessionRepository;
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
            $data = $request->all();
            $this->userAuthValidator->validate($data);

            $view = null;
            $token = $this->generateToken();

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

            $session = [
                'user_id'    => $user->id,
                'token'      => $token,
                'body'       => $data,
                'expired_at' => Carbon::now()->addYears(10)
            ];
            $this->userSessionRepository->create($session);

            return new Response(compact('token', 'view'));
        }, [$request]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
