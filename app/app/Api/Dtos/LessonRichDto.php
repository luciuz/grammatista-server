<?php

namespace App\Api\Dtos;

/**
 * Class LessonRichDto
 * @package App\Api\Dtos
 */
class LessonRichDto
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var array */
    private $body;

    /** @var bool */
    private $isBookmark;

    /** @var bool */
    private $isComplete;
}
