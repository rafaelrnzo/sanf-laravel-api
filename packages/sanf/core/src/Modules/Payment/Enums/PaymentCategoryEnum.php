<?php

namespace Sanf\Core\Modules\Payment\Enums;

use MyCLabs\Enum\Enum;

class PaymentCategoryEnum extends Enum
{
    public const INSTALLMENT_BILL = 'INSTALLMENT_BILL';
    public const DOWN_PAYMENT_BILL = 'DOWN_PAYMENT_BILL';
}
