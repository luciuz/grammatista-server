<?php

namespace App\Api\Responses;

/**
 * Class BadRequestResponse
 * @package App\Api\Responses
 */
class BadRequestResponse extends Response
{
    private const CODE = 400;

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
