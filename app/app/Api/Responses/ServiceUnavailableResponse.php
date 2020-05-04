<?php

namespace App\Api\Responses;

/**
 * Class ServiceUnavailableResponse
 * @package App\Api\Responses
 */
class ServiceUnavailableResponse extends Response
{
    private const CODE    = 503;
    private const MESSAGE = 'Service unavailable.';

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
