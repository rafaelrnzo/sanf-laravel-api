<?php

namespace Sanf\Core\Modules\Project\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateProjectDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $timestamp;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
