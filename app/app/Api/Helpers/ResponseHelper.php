<?php

namespace App\Api\Helpers;

use App\Api\Responses\InternalErrorResponse;
use App\Api\Responses\Response;
use App\Api\Responses\BadRequestResponse;
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
     * @param callable $callable
     * @param array    $params
     * @return Response
     */
    public function run(callable $callable, array $params): Response
    {
        try {
            return $callable(...$params);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            return new BadRequestResponse(compact('errors'));
        } catch (\Throwable $e) {
            $this->logger->error(
                'Exception in ResponseHelper::run.',
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
