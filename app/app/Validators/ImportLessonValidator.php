<?php

namespace App\Validators;

use App\Enums\Locale;

/**
 * Class UserAuthValidator
 * @package App\Validators
 */
class ImportLessonValidator extends AbstractValidator
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:sP';

    public const OPTIONS = 'options';
    public const LESSON  = 'lesson';
    public const TEST    = 'test';
    public const ANSWER  = 'answer';

    public const OPTION_TITLE         = 'TITLE';
    public const OPTION_LOCALE        = 'LOCALE';
    public const OPTION_TEST_DURATION = 'TEST_DURATION';
    public const OPTION_PUBLISHED_AT  = 'PUBLISHED_AT';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::OPTIONS                                    => 'required|array',
            self::OPTIONS . '.' . self::OPTION_TITLE         => 'required|string|min:10|max:255',
            self::OPTIONS . '.' . self::OPTION_LOCALE        => 'required|in:' . implode(',', Locale::getAll()),
            self::OPTIONS . '.' . self::OPTION_TEST_DURATION => 'required|integer|min:0',
            self::OPTIONS . '.' . self::OPTION_PUBLISHED_AT  => 'nullable|date_format:' . self::DATE_FORMAT,
            self::LESSON           => 'required|array',
            self::LESSON . '.list' => 'required|array',
            self::TEST           => 'required|array',
            self::TEST . '.list' => 'required|array',
            self::ANSWER           => 'required|array',
            self::ANSWER . '.list' => 'required|array',
        ];
    }
}
