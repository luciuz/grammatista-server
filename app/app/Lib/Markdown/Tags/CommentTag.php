<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class CommentTag
 * @package App\Lib\Markdown\Tags
 */
class CommentTag extends AbstractTag
{
    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'c';
    }

    /**
     * @param string $line
     * @return array|null
     */
    public function parse(string $line): ?array
    {
        if (!preg_match('~^\[\/\/\]: # \(([^\)]+)\)$~', $line, $match)) {
            return null;
        }
        return [self::getTagName() => $match[1]];
    }
}
