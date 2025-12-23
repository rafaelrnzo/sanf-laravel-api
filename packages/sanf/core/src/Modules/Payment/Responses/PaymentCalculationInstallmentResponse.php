<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentCalculationInstallmentResponse extends DataTransferObject
{
    public string $contract_no; //": "21KON98123",
    public int $due_date; //": 1765238400,
    public float $total_amount; //": 445000,
    public float $subtotal_installment; //": 220000,
    public float $principal_loan; //": 200000,
    public float $interest_amount; //": 20000,
    public float $penalty_fee; //": 0,
    public string $financing_type_id; //": "01",
    public string $financing_type_desc; //": "Harian",

    /**
     * @var array|PaymentCalculationOutstandingInstallmentResponse[]
     */
    public $outstanding_installments;
}
