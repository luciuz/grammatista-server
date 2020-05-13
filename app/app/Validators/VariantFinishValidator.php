<?php

namespace App\Validators;

/**
 * Class VariantFinishValidator
 * @package App\Validators
 */
class VariantFinishValidator extends AbstractValidator
{
    private const ID = 'id';
    private const USER_ANSWER = 'userAnswer';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::ID => 'required|int',
            self::USER_ANSWER           => 'required|array',
            self::USER_ANSWER . '.list' => 'required|array',
        ];
    }
}
