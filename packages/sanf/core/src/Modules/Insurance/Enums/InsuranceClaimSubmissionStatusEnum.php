<?php

namespace Sanf\Core\Modules\Insurance\Enums;


use MyCLabs\Enum\Enum;

class InsuranceClaimSubmissionStatusEnum extends Enum
{
    const PROCESSED = 10;
    const ACCEPTED = 20;
    const REJECTED = 30;

    public function getTranslation()
    {
        return __('core::constant.insurance_claim_submission.' . $this->getKey());
    }
}
