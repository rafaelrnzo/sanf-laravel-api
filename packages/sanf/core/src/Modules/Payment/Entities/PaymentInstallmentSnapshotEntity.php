<?php

namespace Sanf\Core\Modules\Payment\Entities;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

final class PaymentInstallmentSnapshotEntity extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $contract_no;
    public string $due_date;
    public int $total_amount;
    public int $subtotal_installment;
    public int $principal_loan;
    public int $interest_amount;
    public int $penalty_fee;
    public string $financing_type_id;
    public string $financing_type_desc;
    public ?int $sequence_no;
    public ?int $sequence_total;

    /**
     * @var \Sanf\Core\Modules\Payment\Entities\PaymentInstallmentOutstandingSnapshotEntity[]
     */
    public $outstanding_installments;
}
