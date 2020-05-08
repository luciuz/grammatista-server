<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class H3Tag
 * @package App\Lib\Markdown\Tags
 */
class H3Tag extends AbstractTag
{
    /**
     * @return string
     */
    public function getTagName(): string
    {
        return 'h3';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): array
    {
        return [$this->getTagName() => substr($line, 4)];
    }
}
