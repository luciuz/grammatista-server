<?php

namespace App\Api\Controllers;

use App\Api\Dtos\BookmarkListDto;
use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\BadRequestResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\Services\BookmarkService;
use App\Services\Idempotent\IdempotentMutexException;
use App\Exceptions\IdempotentException;
use App\Services\Idempotent\IdempotentService;
use App\Validators\ListValidator;
use App\Validators\BookmarkSetDelValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class BookmarkController
 * @package App\Api\Controllers
 */
class BookmarkController extends BaseController
{
    /** @var BookmarkService */
    private $bookmarkService;

    /** @var BookmarkSetDelValidator */
    private $bookmarkSetDelValidator;

    /** @var ListValidator */
    private $bookmarkListValidator;

    /** @var IdempotentService */
    private $idempotentService;

    /** @var ResponseHelper */
    private $responseHelper;

    /**
     * @param BookmarkService         $bookmarkService
     * @param BookmarkSetDelValidator $bmSetDelValidator
     * @param ListValidator           $bookmarkListValidator
     * @param IdempotentService       $idempotentService
     * @param ResponseHelper          $responseHelper
     */
    public function __construct(
        BookmarkService $bookmarkService,
        BookmarkSetDelValidator $bmSetDelValidator,
        ListValidator $bookmarkListValidator,
        IdempotentService $idempotentService,
        ResponseHelper $responseHelper
    ) {
        $this->bookmarkService = $bookmarkService;
        $this->bookmarkSetDelValidator = $bmSetDelValidator;
        $this->bookmarkListValidator = $bookmarkListValidator;
        $this->idempotentService = $idempotentService;
        $this->responseHelper = $responseHelper;
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function actionSet(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->bookmarkSetDelValidator->validate($data);

            $userId = \Auth::user()->getAuthIdentifier();
            $lessonId = $data['lessonId'];
            try {
                $result = $this->idempotentService
                    ->runIdempotent($data['transactionToken'], [$this, 'set'], [$userId, $lessonId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            } catch (IdempotentException $e) {
                return new BadRequestResponse($e->getMessage());
            }
        }, [$request]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function actionDelete(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->bookmarkSetDelValidator->validate($data);

            $userId = \Auth::user()->getAuthIdentifier();
            $lessonId = $data['lessonId'];

            try {
                $result = $this->idempotentService
                    ->runIdempotent($data['transactionToken'], [$this, 'delete'], [$userId, $lessonId]);
                return new Response($result);
            } catch (IdempotentMutexException $e) {
                return new ServiceUnavailableResponse();
            } catch (IdempotentException $e) {
                return new BadRequestResponse($e->getMessage());
            }
        }, [$request]);
    }

    /**
     * @see BookmarkListDto
     * @param Request $request
     * @return Response
     */
    public function actionList(Request $request): Response
    {
        return $this->responseHelper->run(function ($request) {
            $data = $request->all();
            $this->bookmarkListValidator->validate($data);

            $result = $this->list($data);
            return new Response($result);
        }, [$request]);
    }

    /**
     * @param int $userId
     * @param int $lessonId
     * @return array|null
     * @throws IdempotentException
     */
    public function set(int $userId, int $lessonId): ?array
    {
        $repository = $this->bookmarkService->getRepository();
        if ($repository->existsByUserIdLessonId($userId, $lessonId)) {
            throw new IdempotentException('Bookmark already exists.', 400);
        }

        $repository->createByUserIdLessonId($userId, $lessonId);
        return null;
    }

    /**
     * @param int $userId
     * @param int $lessonId
     * @return array|null
     * @throws IdempotentException
     */
    public function delete(int $userId, int $lessonId): ?array
    {
        $repository = $this->bookmarkService->getRepository();
        if (!$repository->existsByUserIdLessonId($userId, $lessonId)) {
            throw new IdempotentException('Bookmark does not exist.', 400);
        }

        $repository->deleteByUserIdLessonId($userId, $lessonId);
        return null;
    }

    /**
     * @param array $data
     * @return array
     */
    private function list(array $data): array
    {
        return $this->bookmarkService->list(
            $data['maxId'] ?? null,
            \Auth::user()->getAuthIdentifier()
        );
    }
}
