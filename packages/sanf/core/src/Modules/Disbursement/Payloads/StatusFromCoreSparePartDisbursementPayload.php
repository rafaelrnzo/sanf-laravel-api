<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

final class StatusFromCoreSparePartDisbursementPayload extends DataTransferObject
{
    public string $batch_id;

    /**
     * @var string|int
     */
    public $cust_id;

    public string $cust_id_sanfind;
    public string $status_batch_id;
    public string $status_batch_desc;
}
