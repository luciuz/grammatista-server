<?php

namespace App\Api\Responses;

/**
 * Class ForbiddenResponse
 * @package App\Api\Responses
 */
class ForbiddenResponse extends Response
{
    private const CODE    = 403;
    private const MESSAGE = 'Forbidden.';

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
