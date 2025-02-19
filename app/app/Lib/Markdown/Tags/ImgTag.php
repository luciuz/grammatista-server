<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class ImgTag
 * @package App\Lib\Markdown\Tags
 */
class ImgTag extends AbstractTag
{
    /** @var string|null */
    private $tail;

    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'i';
    }

    /**
     * @param string $line
     * @return array|null
     */
    public function parse(string $line): ?array
    {
        if (!preg_match('~^!\[([^\]]+)\]\(([^\)]+)\)(.+)?~', $line, $match)) {
            return null;
        }
        [, $alt, $src] = $match;
        $this->tail = $match[3] ?? null;
        return [self::getTagName() => compact('alt', 'src')];
    }

    /**
     * @return string|null
     */
    public function getTail(): ?string
    {
        return $this->tail;
    }
}
