<?php

namespace Sanf\Core\Modules\Installment\Payloads;

use Sanf\Core\Traits\CastsNumericDtoProperties;
use Spatie\DataTransferObject\DataTransferObject;

class SendPushNotificationBillChangeJobPayload extends DataTransferObject
{
    use CastsNumericDtoProperties;

    public string $contract_no;

    public string $due_date; // Y-m-d

    public string $customer_id_sanfind;

    public int $tenor_value;

    public string $tenor_unit;
}
