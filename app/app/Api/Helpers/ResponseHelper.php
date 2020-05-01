<?php

namespace App\Api\Helpers;

use App\Api\Responses\InternalErrorResponse;
use App\Api\Responses\Response;
use App\Api\Responses\ValidationErrorResponse;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * Class ResponseHelper
 * @package App\Api\Helpers
 */
class ResponseHelper
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Closure $closure
     * @return Response
     */
    public function run(\Closure $closure): Response
    {
        try {
            return $closure();
        } catch (ValidationException $e) {
            $errors = $e->errors();
            return new ValidationErrorResponse(compact('errors'));
        } catch (\Throwable $e) {
            $this->logger->error(
                'Exception in LessonController::actionSearch.',
                [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine()
                ]
            );
            return new InternalErrorResponse();
        }
    }
}
