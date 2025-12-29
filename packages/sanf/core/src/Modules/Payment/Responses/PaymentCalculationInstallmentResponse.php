<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class PaymentCalculationInstallmentResponse extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $contract_no;
    public int $due_date;
    public int $total_amount;
    public int $subtotal_installment;
    public int $principal_loan;
    public int $interest_amount;
    public int $penalty_fee;
    public string $financing_type_id; // "01"|"02"|"03",
    public string $financing_type_desc; // "Harian"|"Bulanan"|"DP",
    public ?int $sequence_no;
    public ?int $sequence_total;

    /**
     * @var \Sanf\Core\Modules\Payment\Responses\PaymentCalculationOutstandingInstallmentResponse[]
     */
    public $outstanding_installments;
}
