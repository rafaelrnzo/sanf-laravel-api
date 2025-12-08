<?php

namespace Sanf\Core\Modules\Payment\Enums;

use MyCLabs\Enum\Enum;

class PaymentStatusEnum extends Enum
{
    public const PENDING = 'PENDING';
    public const SUCCESS = 'SUCCESS';
    public const FAILED = 'FAILED';
    public const EXPIRED = 'EXPIRED';
    public const CANCELLED = 'CANCELLED';
}
