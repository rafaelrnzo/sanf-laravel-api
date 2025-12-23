<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class ApprovalSparePartDisbursementPayload extends DataTransferObject
{
    public string $profileXid;
    public string $disbursementXid;
    /**
     * APPROVE_ALL | REJECT_SELECTED.
     * @var string
     */
    public string $action;
    /** @var array|string|null */
    public $invoiceXids;
    public ?string $note;
}
