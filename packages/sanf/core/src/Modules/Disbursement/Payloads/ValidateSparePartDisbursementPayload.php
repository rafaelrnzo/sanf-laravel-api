<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ValidateSparePartDisbursementPayload extends DataTransferObject
{
    public string $batch_id;
    public ?bool $validation_complete;

    /**
     * @var \Sanf\Core\Modules\Disbursement\Payloads\ValidateSparePartDisbursementCustomerPayload[]
     */
    public array $customers;
}
