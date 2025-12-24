<?php

namespace Sanf\Core\Modules\Payment\Responses;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class CreateInstallmentPaymentResponse extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $xid;
    public int $total_payment;
    public int $subtotal_all_installment;
    public int $admin_fee;
    public int $discount;
    public ?int $custom_amount;
    public ?int $custom_penalty_amount;
    public string $currency;
    public string $type;
    public string $status;
    public array $installments;
    public int $due_date;
    public int $created_at;
    public ?int $updated_at;
}
