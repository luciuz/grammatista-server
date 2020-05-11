<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class ListNumberedTag
 * @package App\Lib\Markdown\Tags
 */
class ListNumberedTag extends AbstractTag
{
    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'ln';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): array
    {
        return [self::getTagName() => substr($line, 3)];
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return true;
    }
}
