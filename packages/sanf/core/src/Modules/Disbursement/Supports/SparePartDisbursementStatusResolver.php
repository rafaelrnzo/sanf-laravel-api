<?php

namespace Sanf\Core\Modules\Disbursement\Supports;

use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Integration\Modules\SanfCore\Enums\SanfCoreSparePartDisbursementStatusEnum;

final class SparePartDisbursementStatusResolver
{
    public static function mapFromCore(string $statusCore): ?int
    {
        $status = [
            SanfCoreSparePartDisbursementStatusEnum::WAITING_VALIDATION => SparePartDisbursementStatusEnum::WAITING_VALIDATION,
            SanfCoreSparePartDisbursementStatusEnum::VALID => SparePartDisbursementStatusEnum::PAYMENT_COMPLETED,
            SanfCoreSparePartDisbursementStatusEnum::NOT_VALID => SparePartDisbursementStatusEnum::REJECTED,
        ];

        return $status[$statusCore] ?? null;
    }
}
