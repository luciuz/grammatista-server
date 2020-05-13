<?php

namespace App\Api\Controllers;

use App\Api\Dtos\AuthDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\ForbiddenResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\Exceptions\ForbiddenException;
use App\Repositories\UserRepository;
use App\Repositories\UserSessionRepository;
use App\Services\Idempotent\IdempotentMutexException;
use App\Services\Idempotent\IdempotentService;
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

    /** @var UserAuthValidator */
    private $userAuthValidator;

    /** @var IdempotentService */
    private $idempotentService;

    /** @var ResponseHelper */
    private $responseHelper;

    /**
     * @param UserRepository        $userRepository
     * @param UserSessionRepository $userSessionRepository
     * @param UserAuthValidator     $userAuthValidator
     * @param IdempotentService     $idempotentService
     * @param ResponseHelper        $responseHelper
     */
    public function __construct(
        UserRepository $userRepository,
        UserSessionRepository $userSessionRepository,
        UserAuthValidator $userAuthValidator,
        IdempotentService $idempotentService,
        ResponseHelper $responseHelper
    ) {
        $this->userRepository = $userRepository;
        $this->userSessionRepository = $userSessionRepository;
        $this->userAuthValidator = $userAuthValidator;
        $this->idempotentService = $idempotentService;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @see AuthDto
     * @param Request $request
     * @return Response
     */
    public function actionAuth(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->userAuthValidator->validate($data);

            try {
                $result = $this->idempotentService->runIdempotent($data['transactionToken'], [$this, 'auth'], [$data]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            } catch (ForbiddenException $e) {
                return new ForbiddenResponse();
            }
        }, [$request]);
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function auth(array $data): array
    {
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
            throw new ForbiddenException('User blocked.');
        }

        $session = [
            'user_id'    => $user->id,
            'token'      => $token,
            'body'       => $data,
            'expired_at' => Carbon::now()->addYears(10)
        ];
        $this->userSessionRepository->create($session);
        return compact('token', 'view');
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
