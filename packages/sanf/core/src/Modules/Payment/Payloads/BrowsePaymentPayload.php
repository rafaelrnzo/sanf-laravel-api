<?php

namespace Sanf\Core\Modules\Payment\Payloads;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowsePaymentPayload extends CamelCaseDataTransferObject
{
    public int $userAuthId;
    public string $userProfileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $status;
    public ?string $contractNo;
}
