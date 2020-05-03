<?php

namespace App\Api\Responses;

/**
 * Class UnauthorizedResponse
 * @package App\Api\Responses
 */
class UnauthorizedResponse extends Response
{
    private const CODE    = 401;
    private const MESSAGE = 'Unauthorized.';

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
