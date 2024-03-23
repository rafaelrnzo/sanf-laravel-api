<?php

namespace Sanf\Core\Modules\Contract\Enums;

use MyCLabs\Enum\Enum;

class FinancingUnitLocationSubmissionStatusEnum extends Enum
{
    const IN_PROGRESS = 10;
    const ACCEPTED = 20;
    const REJECTED = 30;

    const ALL = [
        self::IN_PROGRESS,
        self::ACCEPTED,
        self::REJECTED,
    ];

    public function getTranslation()
    {
        return __('core::constant.financing_unit_location_submission.' . $this->getKey());
    }
}
