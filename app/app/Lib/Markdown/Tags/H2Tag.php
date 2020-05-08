<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class H2Tag
 * @package App\Lib\Markdown\Tags
 */
class H2Tag extends AbstractTag
{
    /**
     * @return string
     */
    public function getTagName(): string
    {
        return 'h2';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): array
    {
        return [$this->getTagName() => substr($line, 3)];
    }
}
