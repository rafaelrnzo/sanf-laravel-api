<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentInstallmentPayload extends DataTransferObject
{
    public string $contract_no;
    public int $due_date;
}
