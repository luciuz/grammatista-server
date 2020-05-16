<?php

namespace App\Api\Dtos;

/**
 * Class BookmarkListDto
 * @package App\Api\Dtos
 */
class ResultListDto
{
    /** @var ResultItemDto[] */
    private $list;

    /** @var int|null */
    private $rowsLeft;

    /** @var int|null */
    private $maxId;
}
