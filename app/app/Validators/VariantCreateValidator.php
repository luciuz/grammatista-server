<?php

namespace App\Validators;

/**
 * Class VariantCreateValidator
 * @package App\Validators
 */
class VariantCreateValidator extends AbstractValidator
{
    private const LESSON_ID = 'lessonId';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::LESSON_ID => 'required|int',
        ];
    }
}
