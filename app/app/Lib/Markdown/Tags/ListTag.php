<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class ListTag
 * @package App\Lib\Markdown\Tags
 */
class ListTag extends AbstractTag
{
    /**
     * @return string
     */
    public function getTagName(): string
    {
        return 'l';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): array
    {
        return [$this->getTagName() => substr($line, 2)];
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return true;
    }
}
