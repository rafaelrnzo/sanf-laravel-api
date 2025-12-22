<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentPreviewInstallmentEntity extends DataTransferObject
{
    public string $contract_no;

    /**
     * e.g. "2025-12-19T00:00:00+07:00".
     * @var string
     */
    public string $due_date;
}
