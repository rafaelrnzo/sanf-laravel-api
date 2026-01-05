<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ValidateSparePartDisbursementCustomerSummaryPayload extends DataTransferObject
{
    public int $total_invoice;
    public int $approved;
    public int $rejected;
}
