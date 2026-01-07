<?php

namespace Sanf\Integration\Modules\Midtrans\Enums;

use MyCLabs\Enum\Enum;

final class MidtransTransactionStatusEnum extends Enum
{
    public const AUTHORIZE = 'authorize';
    public const CAPTURE = 'capture';
    public const SETTLEMENT = 'settlement';
    public const DENY = 'deny';
    public const PENDING = 'pending';
    public const CANCEL = 'cancel';
    public const REFUND = 'refund';
    public const PARTIAL_REFUND = 'partial_refund';
    public const CHARGEBACK = 'chargeback';
    public const PARTIAL_CHARGEBACK = 'partial_chargeback';
    public const EXPIRE = 'expire';
    public const FAILURE = 'failure';
}
