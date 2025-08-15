<?php

namespace Sanf\Core\Modules\PdcHold\Enums;

use MyCLabs\Enum\Enum;

/**
 * @since CR2025
 */
final class PdcHoldTypeEnum extends Enum
{
    const MULTI_GIRO = 1;
    const MULTI_CONTRACT = 2;
    const RESUME = 3;
    const ALL_TYPES = [self::MULTI_GIRO, self::MULTI_CONTRACT, self::RESUME];

    public function getLabel()
    {
        return __('core::constant.pdc_hold_type.' . $this->getKey());
    }
}
