<?php


namespace Sanf\Core\Modules\Commodity\Dto;


use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateCommodityDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
