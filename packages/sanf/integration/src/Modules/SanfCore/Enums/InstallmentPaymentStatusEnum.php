<?php

namespace Sanf\Integration\Modules\SanfCore\Enums;

use MyCLabs\Enum\Enum;

class InstallmentPaymentStatusEnum extends Enum
{
    public const BELUM_LUNAS = 0;
    public const LUNAS = 1;
    public const MENUNGGU_KONFIRMASI = 2;
}
