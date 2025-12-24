<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Spatie\DataTransferObject\DataTransferObject;

final class PaymentInstallmentOutstandingSnapshotEntity extends DataTransferObject
{
    public string $due_date;
    public int $total;
    public int $principal_loan;
    public int $interest_amount;
    public int $penalty_fee;
}
