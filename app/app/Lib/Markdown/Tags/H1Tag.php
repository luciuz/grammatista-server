<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class H1Tag
 * @package App\Lib\Markdown\Tags
 */
class H1Tag extends AbstractTag
{
    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'h1';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): array
    {
        return [self::getTagName() => substr($line, 2)];
    }
}
