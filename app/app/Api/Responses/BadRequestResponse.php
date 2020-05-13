<?php

namespace App\Api\Responses;

/**
 * Class BadRequestResponse
 * @package App\Api\Responses
 */
class BadRequestResponse extends Response
{
    private const CODE    = 400;
    private const MESSAGE = 'Bad request.';

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
