<?php

namespace App\Api\Dtos;

/**
 * Class BookmarkListDto
 * @package App\Api\Dtos
 */
class BookmarkListDto
{
    /** @var BookmarkItemDto[] */
    private $list;

    /** @var int|null */
    private $rowsLeft;

    /** @var int|null */
    private $maxId;
}
