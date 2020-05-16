<?php

namespace App\Api\Responses;

/**
 * Class TooManyRequestsResponse
 * @package App\Api\Responses
 */
class TooManyRequestsResponse extends Response
{
    private const CODE    = 429;
    private const MESSAGE = 'Too many requests.';

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
