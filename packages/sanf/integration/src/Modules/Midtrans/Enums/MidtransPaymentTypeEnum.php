<?php

namespace Sanf\Integration\Modules\Midtrans\Enums;

use MyCLabs\Enum\Enum;

class MidtransPaymentTypeEnum extends Enum
{
    public const BANK_TRANSFER = 'bank_transfer';
    public const ECHANNEL = 'echannel';
}
