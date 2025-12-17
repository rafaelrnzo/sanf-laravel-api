<?php

namespace Sanf\Core\Modules\Disbursement\Payloads;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseSparePartDisbursementPayload extends CamelCaseDataTransferObject
{
    public string $profileXid;
    public ?string $plafondXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $statusId;
    /**
     * HISTORICAL | NEED_APPROVAL.
     * @var string
     */
    public string $listType;
}
