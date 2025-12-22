<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentPreviewOutstandingInstallmentResponse extends DataTransferObject
{
    public int $due_date;
    public float $total;
    public float $principal_loan;
    public float $interest_amount;
    public float $penalty_fee;
}
