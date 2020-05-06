<?php

namespace App\Api\Dtos;

/**
 * Class LessonSearchDto
 * @package App\Api\Dtos
 */
class LessonSearchDto
{
    /** @var LessonItemDto[] */
    private $list;

    /** @var int */
    private $totalRows;

    /** @var int|null */
    private $maxId;
}
