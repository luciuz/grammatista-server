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
    private const F_VK_USER_ID = 'vk_user_id';
    private const F_SIGN = 'sign';

    /** @var VkSignRule */
    private $vkSignRule;

    /** @var array */
    private $params;

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
            self::F_VK_USER_ID => 'required|int',
            self::F_SIGN       => ['required', 'string', $this->vkSignRule],
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
