<?php

namespace Sanf\Core\Modules\Installment\Support;

use Sanf\Core\Modules\Installment\Enums\InstallmentStatusEnum;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentPaymentStatusEnum;

class InstallmentStatusMapper
{
    /**
     * @param int $coreStatus
     * @param string|null $dbStatus
     * @param PaymentModel|null $payment fill as null if there is no payment created
     * @return string
     */
    public static function map(int $coreStatus, ?string $dbStatus = null, ?PaymentModel $payment = null): string
    {
        if ($coreStatus === InstallmentPaymentStatusEnum::LUNAS) {
            return InstallmentStatusEnum::PAID;
        }

        if ($coreStatus === InstallmentPaymentStatusEnum::MENUNGGU_KONFIRMASI) {
            return InstallmentStatusEnum::IN_PROGRESS;
        }

        if ($dbStatus === InstallmentStatusEnum::WAITING_PAYMENT) {
            return $dbStatus;
        }

        if (
            $coreStatus === InstallmentPaymentStatusEnum::BELUM_LUNAS
            && $payment !== null
            && $payment->status === PaymentStatusEnum::SUCCESS
            && $payment->core_installment_submitted === false
        ) {
            return InstallmentStatusEnum::IN_PROGRESS;
        }

        return InstallmentStatusEnum::ACTIVE;
    }
}
