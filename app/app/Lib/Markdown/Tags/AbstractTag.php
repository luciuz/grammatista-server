<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class AbstractTag
 * @package App\Lib\Markdown\Tags
 */
abstract class AbstractTag
{
    /**
     * @return string
     */
    abstract public static function getTagName(): string;

    /**
     * @param string $line
     * @return array|null
     */
    abstract public function parse(string $line): ?array;

    /**
     * @return string|null
     */
    public function getTail(): ?string
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return false;
    }
}
