<?php

namespace Sanf\Core\Modules\Installment\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class SendPushNotificationInstallmentJobPayload extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $contract_no;

    public string $due_date; // Y-m-d

    public float $total_amount;

    public string $customer_id_sanfind;
}
