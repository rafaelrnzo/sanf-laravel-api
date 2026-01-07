<?php

namespace Sanf\Core\Modules\Installment\Enums;

use MyCLabs\Enum\Enum;

class InstallmentStatusEnum extends Enum
{
    public const ACTIVE = 'ACTIVE';
    public const WAITING_PAYMENT = 'WAITING_PAYMENT';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const PAID = 'PAID';
}
