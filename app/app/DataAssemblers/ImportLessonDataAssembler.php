<?php

namespace App\DataAssemblers;

/**
 * Class ImportLessonDataAssembler
 * @package App\DataAssemblers
 */
class ImportLessonDataAssembler
{
    private const SECTION_TEMPLATE = '#####%s#####';

    private const SECTION_OPTIONS = 'options';
    private const SECTION_LESSON  = 'lesson';
    private const SECTION_TEST    = 'test';
    private const SECTION_ANSWER  = 'answer';

    private const SECTION_TAG  = 'c';
    private const OPTIONS_TAG  = 'l';
    private const QUESTION_TAG  = 'h3';
    private const QUESTION_OPTIONS_TAG  = 'ln';
    private const ANSWERS_TAG  = 'ln';

    /** @var array */
    private $result;

    /** @var string */
    private $section;

    /** @var array */
    private $currentQuestion;

    /**
     * @param array $lines
     * @return array
     */
    public function make(array $lines): array
    {
        $this->result = [];
        $this->section = null;
        foreach ($lines as $line) {
            if (
                $this->checkSection($line, self::SECTION_OPTIONS)
                || $this->checkSection($line, self::SECTION_LESSON)
                || $this->checkSection($line, self::SECTION_TEST)
                || $this->checkSection($line, self::SECTION_ANSWER)
            ) {
                continue;
            }
            if (
                $this->continueFromOptions($line)
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
     * @param array  $line
     * @param string $section
     * @return bool
     */
    private function checkSection(array $line, string $section): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($key === self::SECTION_TAG && $value === $this->wrapSection($section)) {
            $this->section = $section;
            return true;
        }

        return false;
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromOptions(array $line): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($this->section === self::SECTION_OPTIONS && $key === self::OPTIONS_TAG) {
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
        if ($this->section === self::SECTION_LESSON) {
            $this->result[$this->section]['list'][] = $line;
            return true;
        }

        return false;
    }

    /**
     * @param array $line
     * @return bool
     */
    private function continueFromTest(array $line): bool
    {
        $key = array_key_first($line);
        $value = $line[$key];
        if ($this->section === self::SECTION_TEST) {
            if ($key === self::QUESTION_TAG) {
                $this->currentQuestion = [];
                $this->currentQuestion['title'] = $value;
            } elseif ($key === self::QUESTION_OPTIONS_TAG) {
                $this->currentQuestion['options'] = $value;
                $this->result[$this->section]['list'][] = $this->currentQuestion;
            }
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
        if ($this->section === self::SECTION_ANSWER && $key === self::ANSWERS_TAG) {
            foreach ($value as $items) {
                $answers = array_map('intval', explode(',', $items));
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
