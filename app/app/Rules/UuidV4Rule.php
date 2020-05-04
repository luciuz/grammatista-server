<?php

namespace App\Rules;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class UuidV4Rule
 * @package App\Rules
 */
class UuidV4Rule implements Rule
{
    /**
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     * @phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
     */
    public function passes($attribute, $value): bool
    {
        try {
            $uuid = Uuid::fromString($value);
            $fields = $uuid->getFields();
            if ($fields instanceof FieldsInterface) {
                return $fields->getVersion() === Uuid::UUID_TYPE_RANDOM;
            }
        } catch (InvalidUuidStringException $e) {
        }

        return false;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.custom.invalid_uuidv4');
    }
}
