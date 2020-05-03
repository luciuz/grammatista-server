<?php

namespace App\Validators;

/**
 * Class LessonGetValidator
 * @package App\Validators
 */
class LessonGetValidator extends AbstractValidator
{
    private const ID = 'id';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::ID => 'required|int',
        ];
    }
}
