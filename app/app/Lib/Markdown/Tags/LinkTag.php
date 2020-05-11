<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class LinkTag
 * @package App\Lib\Markdown\Tags
 */
class LinkTag extends AbstractTag
{
    /** @var string|null */
    private $tail;

    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 'a';
    }

    /**
     * @param string $line
     * @return array|null
     */
    public function parse(string $line): ?array
    {
        if (!preg_match('~^\[([^\]]+)\]\(([^\)]+)\)(.+)?$~', $line, $match)) {
            return null;
        }
        $text = $match[1];
        $link = $match[2];
        $this->tail = $match[3] ?? null;
        return [self::getTagName() => compact('text', 'link')];
    }

    /**
     * @return string|null
     */
    public function getTail(): ?string
    {
        return $this->tail;
    }
}
