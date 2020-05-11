<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class TextTag
 * @package App\Lib\Markdown\Tags
 */
class TextTag extends AbstractTag
{
    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'p';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): ?array
    {
        if ($line) {
            return [self::getTagName() => $line];
        }
        return null;
    }
}
