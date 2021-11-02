<?php


namespace Sanf\Core\Modules\Financing\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowseFinancingApplicationDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $skip = 10;
    public ?int $limit = 0;
    public ?string $sortBy;
    public ?string $keyword;
}
