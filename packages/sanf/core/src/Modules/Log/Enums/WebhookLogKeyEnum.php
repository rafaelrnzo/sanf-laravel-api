<?php

namespace Sanf\Core\Modules\Log\Enums;

use MyCLabs\Enum\Enum;

class WebhookLogKeyEnum extends Enum
{
    public const MIDTRANS_STATUS = 'midtrans.status';
    public const SPARE_PART_DISBURSEMENT_VALIDATION = 'sp_disbursement.validation';
    public const SPARE_PART_DISBURSEMENT_STATUS = 'sp_disbursement.status';
    public const SBF_STATUS = 'sbf.status';
}
