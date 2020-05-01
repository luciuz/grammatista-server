<?php

namespace App\Api\Responses;

/**
 * Class InternalErrorResponse
 * @package App\Api\Responses
 */
class InternalErrorResponse extends Response
{
    private const CODE    = 500;
    private const MESSAGE = 'Internal error.';

    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     */
    public function __construct($data = self::MESSAGE, $status = self::CODE, $headers = [], $options = 0)
    {
        parent::__construct($data, $status, $headers, $options);
    }
}
