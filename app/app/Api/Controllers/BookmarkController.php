<?php

namespace App\Api\Controllers;

use App\Api\Helpers\ResponseHelper;
use App\Api\Responses\BadRequestResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ServiceUnavailableResponse;
use App\Repositories\BookmarkRepository;
use App\Services\Idempotent\IdempotentMutexException;
use App\Services\Idempotent\IdempotentException;
use App\Services\Idempotent\IdempotentService;
use App\Validators\BookmarkSetDelValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class BookmarkController
 * @package App\Api\Controllers
 */
class BookmarkController extends BaseController
{
    /** @var BookmarkRepository */
    private $bookmarkRepository;

    /** @var BookmarkSetDelValidator */
    private $bookmarkSetDelValidator;

    /** @var IdempotentService */
    private $idempotentService;

    /** @var ResponseHelper */
    private $responseHelper;

    /**
     * @param BookmarkRepository      $bookmarkRepository
     * @param BookmarkSetDelValidator $bmSetDelValidator
     * @param IdempotentService       $idempotentService
     * @param ResponseHelper          $responseHelper
     */
    public function __construct(
        BookmarkRepository $bookmarkRepository,
        BookmarkSetDelValidator $bmSetDelValidator,
        IdempotentService $idempotentService,
        ResponseHelper $responseHelper
    ) {
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkSetDelValidator = $bmSetDelValidator;
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
     * @param int $userId
     * @param int $lessonId
     * @return array|null
     * @throws IdempotentException
     */
    public function set(int $userId, int $lessonId): ?array
    {
        if ($this->bookmarkRepository->existsByUserIdLessonId($userId, $lessonId)) {
            throw new IdempotentException('Bookmark already exists.', 400);
        }

        $this->bookmarkRepository->createByUserIdLessonId($userId, $lessonId);
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
        if (!$this->bookmarkRepository->existsByUserIdLessonId($userId, $lessonId)) {
            throw new IdempotentException('Bookmark does not exist.', 400);
        }

        $this->bookmarkRepository->deleteByUserIdLessonId($userId, $lessonId);
        return null;
    }
}
