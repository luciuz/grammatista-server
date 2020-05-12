<?php

namespace App\Lib\Markdown;

use App\Lib\Markdown\Tags\AbstractTag;
use App\Lib\Markdown\Tags\CommentTag;
use App\Lib\Markdown\Tags\H1Tag;
use App\Lib\Markdown\Tags\H2Tag;
use App\Lib\Markdown\Tags\H3Tag;
use App\Lib\Markdown\Tags\ImgTag;
use App\Lib\Markdown\Tags\LinkTag;
use App\Lib\Markdown\Tags\ListNumberedTag;
use App\Lib\Markdown\Tags\ListTag;
use App\Lib\Markdown\Tags\TextTag;

/**
 * Class MarkdownParser
 * @package App\Lib\Markdown
 */
class MarkdownParser
{
    /** @var CommentTag */
    private $commentTag;

    /** @var TextTag */
    private $textTag;

    /** @var H1Tag */
    private $h1Tag;

    /** @var H2Tag */
    private $h2Tag;

    /** @var H3Tag */
    private $h3Tag;

    /** @var ListTag */
    private $listTag;

    /** @var ListNumberedTag */
    private $listNumberedTag;

    /** @var ImgTag */
    private $imgTag;

    /** @var LinkTag */
    private $linkTag;

    /** @var array */
    private $map;

    /** @var array */
    private $result;

    /** @var array */
    private $subResult;

    /** @var string|null */
    private $currentTagClassName;

    /**
     * @param CommentTag      $commentTag
     * @param TextTag         $textTag
     * @param H1Tag           $h1Tag
     * @param H2Tag           $h2Tag
     * @param H3Tag           $h3Tag
     * @param ListTag         $listTag
     * @param ListNumberedTag $listNumberedTag
     * @param ImgTag          $imgTag
     * @param LinkTag         $linkTag
     */
    public function __construct(
        CommentTag $commentTag,
        TextTag $textTag,
        H1Tag $h1Tag,
        H2Tag $h2Tag,
        H3Tag $h3Tag,
        ListTag $listTag,
        ListNumberedTag $listNumberedTag,
        ImgTag $imgTag,
        LinkTag $linkTag
    ) {
        $this->commentTag = $commentTag;
        $this->textTag = $textTag;
        $this->h1Tag = $h1Tag;
        $this->h2Tag = $h2Tag;
        $this->h3Tag = $h3Tag;
        $this->listTag = $listTag;
        $this->listNumberedTag = $listNumberedTag;
        $this->imgTag = $imgTag;
        $this->linkTag = $linkTag;

        $this->map = $this->getMap();
    }

    /**
     * Step 1. Init.
     */
    public function init(): void
    {
        $this->result = [];
        $this->subResult = [];
    }

    /**
     * Step 2. Parse line.
     * @param string $line
     */
    public function parseLine(string $line): void
    {
        $line = rtrim($line);
        $tag = $this->getTag($line);
        if ($this->currentTagClassName !== ($newTag = get_class($tag))) {
            $this->pickSubResult();
            $this->currentTagClassName = $newTag;
        }

        $result = $tag->parse($line);
        if ($result === null) {
            $tag = $this->textTag;
            $result = $tag->parse($line);
        }
        if ($result === null) {
            return;
        }
        if ($tag->isSet()) {
            $this->subResult[$tag::getTagName()][] = reset($result);
        } else {
            $this->result[] = $result;
        }

        if ($tail = $tag->getTail()) {
            $this->parseLine($tail);
        }
    }

    /**
     * Step 3. Pick sub result.
     */
    public function pickSubResult(): void
    {
        if ($this->subResult) {
            $this->result[] = $this->subResult;
            $this->subResult = [];
        }
    }

    /**
     * Step 4. Get result.
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param string $line
     * @return AbstractTag
     */
    protected function getTag(string $line): AbstractTag
    {
        $next = false;
        $path = $this->map;
        $tag = $this->textTag;
        $i = 0;
        do {
            $char = mb_substr($line, $i++, 1);
            if (isset($path[$char])) {
                $path = $path[$char];
                if (is_array($path)) {
                    $next = true;
                } elseif ($path instanceof AbstractTag) {
                    $next = false;
                    $tag = $path;
                }
            } elseif (isset($path['default'])) {
                $next = false;
                $tag = $path['default'];
            }
        } while ($next);
        return $tag;
    }

    /**
     * @return array
     */
    protected function getMap(): array
    {
        return [
            '#' => [
                ' ' => $this->h1Tag,
                '#' => [
                    ' ' => $this->h2Tag,
                    '#' => [
                        ' ' => $this->h3Tag,
                    ],
                ],
            ],
            '-' => [
                ' ' => $this->listTag,
            ],
            '1' => [
                '.' => [
                    ' ' => $this->listNumberedTag,
                ],
            ],
            '[' => [
                '/' => [
                    '/' => $this->commentTag,
                ],
                'default' => $this->linkTag,
            ],
            '!' => [
                '[' => $this->imgTag,
            ],
        ];
    }
}
