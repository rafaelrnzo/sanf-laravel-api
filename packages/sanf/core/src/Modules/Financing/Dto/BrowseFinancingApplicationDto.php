<?php


namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseFinancingApplicationDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
