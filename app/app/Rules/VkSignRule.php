<?php

namespace App\Rules;

use App\Lib\VkApi\VkHelper;
use Illuminate\Contracts\Validation\Rule;

class VkSignRule implements Rule
{
    /** @var VkHelper */
    private $vkHelper;

    /** @var array */
    private $params;

    /**
     * @param VkHelper $vkHelper
     */
    public function __construct(VkHelper $vkHelper)
    {
        $this->vkHelper = $vkHelper;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->vkHelper->checkSign($this->params, $value);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.custom.invalid_sign');
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
