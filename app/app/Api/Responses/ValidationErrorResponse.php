<?php

namespace App\Api\Responses;

/**
 * Class ValidationErrorResponse
 * @package App\Api\Responses
 */
class ValidationErrorResponse extends Response
{
    private const CODE = 422;

    /**
     * @param null  $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     */
    public function __construct($data = null, $status = self::CODE, $headers = [], $options = 0)
    {
        parent::__construct($data, $status, $headers, $options);
    }
}
