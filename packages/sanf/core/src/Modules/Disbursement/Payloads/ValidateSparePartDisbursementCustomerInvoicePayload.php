<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ValidateSparePartDisbursementCustomerInvoicePayload extends DataTransferObject
{
    public string $cust_id;
    public string $cust_id_sanfind;
    public string $no_invoice;
    public bool $status;
    public string $message;
}
