<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseFinancingUnitLocationSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public ?int $statusId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $timestamp;
}
