<?php

namespace Sanf\Core\Modules\Prepayment\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseContractByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $timestamp;
}
