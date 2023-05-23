<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductSparePartFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $categoryId;
    public ?int $brandId;
    public ?int $rating;
    public ?int $minPrice;
    public ?int $maxPrice;
    public ?int $merchantId;
}
