<?php

namespace App\Api\Dtos;

/**
 * Class BookmarkItemDto
 * @package App\Api\Dtos
 */
class BookmarkItemDto
{
    /** @var int */
    private $id;

    /** @var int */
    private $lessonId;

    /** @var string */
    private $title;

    /** @var bool */
    private $isComplete;
}
