<?php

namespace Sanf\Core\Modules\Plafond\Enums;

use MyCLabs\Enum\Enum;

final class PlafondDisbursementStatusEnum extends Enum
{
    public const SUBMIT = 10;
    public const ON_PROCESS = 11;
    public const REVISION = 15;
    public const REJECT = 20;
    public const APPROVE = 30;

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return __('core::constant.plafond.disbursement.status.' . $this->getKey());
    }
}
