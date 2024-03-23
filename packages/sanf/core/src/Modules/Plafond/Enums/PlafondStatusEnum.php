<?php

namespace Sanf\Core\Modules\Plafond\Enums;

use MyCLabs\Enum\Enum;

class PlafondStatusEnum extends Enum
{
    public const IN_PROGRESS = '1';
    public const REJECT = '2';
    public const APPROVED = '3';
    public const CLOSED = '4';

    public function getTranslation()
    {
        return __('core::constant.plafond-status.' . $this->getKey());
    }
}
