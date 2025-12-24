<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentInstallmentCalculationPayload extends DataTransferObject
{
    public string $profileXid;
    public ?int $customAmount;
    public ?int $customPenaltyAmount;
    /**
     * @var array|PaymentInstallmentPayload[]
     */
    public array $installments;
    public bool $preferCache = false;
}
