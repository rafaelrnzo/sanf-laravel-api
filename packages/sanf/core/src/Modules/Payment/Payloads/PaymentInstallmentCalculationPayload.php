<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class PaymentInstallmentCalculationPayload extends DataTransferObject
{
    public string $profileXid;
    public ?float $customAmount;
    public ?float $customPenaltyAmount;
    /**
     * @var array|PaymentInstallmentPayload[]
     */
    public array $installments;
    public bool $preferCache = false;
}
