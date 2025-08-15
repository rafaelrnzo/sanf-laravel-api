<?php

namespace Sanf\Core\Modules\PdcHold\Enums;

use MyCLabs\Enum\Enum;

/**
 * @since CR2025
 */
final class PdcHoldStatusEnum extends Enum
{
    const PROCESSED = 10;
    const ACCEPTED = 20;
    const REJECTED = 30;
    const ALL_STATUS = [self::PROCESSED, self::ACCEPTED, self::REJECTED];

    public function getLabel()
    {
        return __('core::constant.pdc_hold_status.' . $this->getKey());
    }
}
