<?php

namespace App\Api\Responses;

/**
 * Class NotFoundResponse
 * @package App\Api\Responses
 */
class NotFoundResponse extends Response
{
    private const CODE    = 404;
    private const MESSAGE = 'Not found.';

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
