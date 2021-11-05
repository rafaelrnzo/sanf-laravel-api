<?php


namespace Sanf\Core\Modules\Commodity\Dto;


use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateUserCommodityDto extends CamelCaseDataTransferObject
{
    public ?int $userId;
    public ?int $timestamp;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
