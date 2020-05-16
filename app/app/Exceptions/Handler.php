<?php

namespace App\Exceptions;

use App\Api\Responses\InternalErrorResponse;
use App\Api\Responses\TooManyRequestsResponse;
use App\Api\Responses\UnauthorizedResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Psr\Log\LoggerInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Container $container
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->logger = $container->make(LoggerInterface::class);
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $e
     * @return \App\Api\Responses\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return new UnauthorizedResponse();
        }
        if ($e instanceof ThrottleRequestsException) {
            return new TooManyRequestsResponse();
        }

        $this->logger->error(
            'Exception in Handler::render.',
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
