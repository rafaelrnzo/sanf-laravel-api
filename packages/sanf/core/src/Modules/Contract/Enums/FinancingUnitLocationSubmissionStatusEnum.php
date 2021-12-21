<?php

namespace Sanf\Core\Modules\Contract\Enums;


use MyCLabs\Enum\Enum;

class FinancingUnitLocationSubmissionStatusEnum extends Enum
{
    const PROCESSED = 10;
    const ACCEPTED = 20;
    const REJECTED = 30;

    public function getTranslation()
    {
        return __('core::constant.financing_unit_location_submission.' . $this->getKey());
    }
}
