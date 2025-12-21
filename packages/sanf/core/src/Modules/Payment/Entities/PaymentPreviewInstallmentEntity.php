<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentPreviewInstallmentEntity extends DataTransferObject
{
    public string $contract_no;
    public string $due_date; // 2025-12-19T00:00:00+07:00
}
