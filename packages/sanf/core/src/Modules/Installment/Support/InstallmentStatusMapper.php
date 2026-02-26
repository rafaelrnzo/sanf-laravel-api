<?php

namespace Sanf\Core\Modules\Installment\Support;

use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;

class InstallmentStatusMapper
{
    public static function map(int $coreStatus, ?string $dbStatus = null): string
    {
        if ($dbStatus === InstallmentStatusEnum::WAITING_PAYMENT || $dbStatus === InstallmentStatusEnum::IN_PROGRESS) {
            return $dbStatus;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::LUNAS) {
            return InstallmentStatusEnum::PAID;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::MENUNGGU_KONFIRMASI) {
            return InstallmentStatusEnum::IN_PROGRESS;
        }

        return InstallmentStatusEnum::ACTIVE;
    }
}
