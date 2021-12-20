<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseFinancingUnitByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $timestamp;
}
