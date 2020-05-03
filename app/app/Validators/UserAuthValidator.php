<?php

namespace App\Validators;

use App\Rules\VkSignRule;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Class UserAuthValidator
 * @package App\Validators
 */
class UserAuthValidator extends AbstractValidator
{
    public const VK_USER_ID = 'vk_user_id';
    public const SIGN       = 'sign';

    /** @var VkSignRule */
    private $vkSignRule;

    /**
     * @param ValidationFactory $validator
     * @param VkSignRule        $vkSignRule
     */
    public function __construct(ValidationFactory $validator, VkSignRule $vkSignRule)
    {
        parent::__construct($validator);
        $this->vkSignRule = $vkSignRule;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::VK_USER_ID => 'required|int',
            self::SIGN       => ['required', 'string', $this->vkSignRule],
        ];
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data): void
    {
        $this->vkSignRule->setParams($data); // set rule params
        parent::validate($data);
    }
}
