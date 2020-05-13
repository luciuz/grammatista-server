<?php

namespace App\Api\Dtos;

/**
 * Class VariantDto
 * @package App\Api\Dtos
 */
class VariantDto
{
    /** @var int */
    private $id;

    /** @var int|null */
    private $expiredAt;

    /** @var int|null */
    private $finishedAt;

    /** @var array */
    private $question;

    /** @var array|null */
    private $result;
}
