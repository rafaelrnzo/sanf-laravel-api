<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class CreateInstallmentPaymentPayload extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public int $userAuthId;
    public string $userProfileXid;
    public int $total_payment;
    public int $subtotal_all_installment;
    public int $discount;
    public int $admin_fee;
    public ?int $custom_amount;
    public ?int $custom_penalty_amount;
    public string $currency;

    /**
     * @var \Sanf\Core\Modules\Payment\Responses\PaymentCalculationInstallmentResponse[]
     */
    public array $installments;
}
