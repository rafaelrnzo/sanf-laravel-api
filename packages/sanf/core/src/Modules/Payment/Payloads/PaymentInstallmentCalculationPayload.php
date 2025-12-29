<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class PaymentInstallmentCalculationPayload extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $profileXid;
    public ?int $customAmount;
    public ?int $customPenaltyAmount;
    /**
     * @var \Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload[]
     */
    public array $installments;
    public bool $preferCache = false;
}
