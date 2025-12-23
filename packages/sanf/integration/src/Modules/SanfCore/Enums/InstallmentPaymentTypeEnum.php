<?php

namespace Sanf\Integration\Modules\SanfCore\Enums;

use MyCLabs\Enum\Enum;

class InstallmentPaymentTypeEnum extends Enum
{
    public const HARIAN = '01';
    public const BULANAN = '02';
    public const DOWN_PAYMENT = '03';
}
