<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowsePlafondDisbursementEncryptedRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public string $plafondXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $statusId;
}
