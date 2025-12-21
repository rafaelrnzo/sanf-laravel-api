<?php

namespace Sanf\Integration\Modules\SanfCore\Enums;

use MyCLabs\Enum\Enum;

class InstallmentPaymentStatusEnum extends Enum
{
    public const UNPAID = 0; // Belum Lunas
    public const PAID = 1; // Lunas
}
