<?php

namespace Sanf\Core\Modules\User\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class EntityType
 * @package Sanf\Core\Modules\User
 */
class UserAuthLogStatusEnum extends Enum
{
    public const SUBMIT = 10;
    public const REJECT = 20;
    public const APPROVE = 30;
    public const ALL = [
        self::SUBMIT,
        self::REJECT,
        self::APPROVE,
    ];

    public function getTranslation()
    {
        return __('core::constant.auth-log-status.' . $this->getKey());
    }
}
