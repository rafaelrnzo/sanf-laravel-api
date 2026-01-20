<?php

namespace Sanf\Core\Modules\Installment\Payloads;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseInstallmentPayload extends CamelCaseDataTransferObject
{
    public string $profileXid;
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $periodType;
    public ?string $contractNo;
}
