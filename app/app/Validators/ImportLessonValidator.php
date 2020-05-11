<?php

namespace App\Validators;

/**
 * Class UserAuthValidator
 * @package App\Validators
 */
class ImportLessonValidator extends AbstractValidator
{
    public const OPTIONS  = 'options';
    public const LESSON   = 'lesson';
    public const QUESTION = 'question';
    public const ANSWER   = 'answer';

    public const OPTION_TITLE         = 'TITLE';
    public const OPTION_LOCALE        = 'LOCALE';
    public const OPTION_TEST_DURATION = 'TEST_DURATION';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::OPTIONS  => 'required|array',
            self::LESSON   => 'required|array',
            self::QUESTION => 'required|array',
            self::ANSWER   => 'required|array',
        ];
    }
}
