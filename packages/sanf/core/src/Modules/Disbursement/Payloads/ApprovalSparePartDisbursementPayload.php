<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ApprovalSparePartDisbursementPayload extends DataTransferObject
{
    public string $profileXid;
    public string $disbursementXid;
    /** @var string APPROVE_ALL | REJECT_SELECTED */
    public string $action;
    /** @var array|string|null */
    public $invoiceXids;
    public ?string $note;
}
