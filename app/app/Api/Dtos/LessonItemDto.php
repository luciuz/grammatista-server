<?php

namespace App\Api\Dtos;

/**
 * Class LessonItemDto
 * @package App\Api\Dtos
 */
class LessonItemDto
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var bool */
    private $isBookmark;

    /** @var bool */
    private $isComplete;
}
