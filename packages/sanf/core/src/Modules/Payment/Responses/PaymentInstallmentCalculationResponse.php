<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class PaymentInstallmentCalculationResponse extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public float $total_payment;
    public float $subtotal_all_installment;
    public float $discount;
    public float $admin_fee;
    public ?float $custom_amount;
    public ?float $custom_penalty_amount;
    public string $currency;

    /**
     * @var array|PaymentCalculationInstallmentResponse[]
     */
    public array $installments;
}
