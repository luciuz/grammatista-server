<?php

namespace App\DataAssemblers;

use App\Lib\Markdown\Tags\CommentTag;
use App\Lib\Markdown\Tags\ListNumberedTag;
use App\Lib\Markdown\Tags\ListTag;

class ImportLessonDataAssembler
{
    private const SECTION_TEMPLATE = '#####%s#####';

    private const SECTION_OPTIONS = 'options';
    private const SECTION_LESSON  = 'lesson';
    private const SECTION_TEST    = 'test';
    private const SECTION_ANSWER  = 'answer';

    /** @var array */
    private $result;

    /** @var string */
    private $section;

    /**
     * @param array $lines
     * @return array
     */
    public function make(array $lines): array
    {
        $this->result = [];
        $this->section = null;
        foreach ($lines as $line) {
            if ($this->continueFromOptions($line)
                || $this->continueFromLesson($line)
                || $this->continueFromTest($line)
                || $this->continueFromAnswer($line)
            ) {
                continue;
            }
        }

        return $this->result;
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromOptions(array $line): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($value === $this->wrapSection(self::SECTION_OPTIONS) && $key === CommentTag::getTagName()) {
            $this->section = self::SECTION_OPTIONS;
            return true;
        }

        if ($this->section === self::SECTION_OPTIONS && $key === ListTag::getTagName()) {
            foreach ($value as $items) {
                [$param, $val] = explode('=', $items);
                $this->result[$this->section][$param] = $val;
            }
            $this->section = null;
            return true;
        }

        return false;
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromLesson(array $line): bool
    {
        return $this->continueFromMarkdown($line, self::SECTION_LESSON);
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromTest(array $line): bool
    {
        return $this->continueFromMarkdown($line, self::SECTION_TEST);
    }

    /**
     * @param array  $line
     * @param string $section
     * @return bool
     */
    private function continueFromMarkdown(array $line, string $section): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($value === $this->wrapSection($section) && $key === CommentTag::getTagName()) {
            $this->section = $section;
            return true;
        }

        if ($this->section === $section) {
            $this->result[$this->section]['list'][] = $line;
            return true;
        }

        return false;
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromAnswer(array $line): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($value === $this->wrapSection(self::SECTION_ANSWER) && $key === CommentTag::getTagName()) {
            $this->section = self::SECTION_ANSWER;
            return true;
        }

        if ($this->section === self::SECTION_ANSWER && $key === ListNumberedTag::getTagName()) {
            foreach ($value as $items) {
                $answers = explode(',', $items);
                $this->result[$this->section]['list'][] = $answers;
            }
            $this->section = null;
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return string
     */
    private function wrapSection(string $name): string
    {
        return sprintf(self::SECTION_TEMPLATE, $name);
    }
}
