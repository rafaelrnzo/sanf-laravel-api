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

    /**
     * Payment transaction was expired but on progress midtrans status check.
     * @var string
     */
    public const EXPIRE_IN_PROGRESS = 'EXPIRE_IN_PROGRESS';

    /**
     * Midtrans payment was successful but the payment transaction was expired.
     * @var string
     */
    public const PAID_LATE = 'PAID_LATE';
}
