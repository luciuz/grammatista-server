<?php

namespace App\Api\Responses;

/**
 * Class UnprocessableEntityResponse
 * @package App\Api\Responses
 */
class UnprocessableEntityResponse extends Response
{
    private const CODE    = 422;
    private const MESSAGE = 'Unprocessable entity.';

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
