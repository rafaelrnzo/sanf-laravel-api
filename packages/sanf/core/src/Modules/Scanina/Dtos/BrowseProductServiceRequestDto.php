<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Sanf\Core\Modules\Scanina\Enums\ScaninaProductSortByEnum;

class BrowseProductServiceRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = ScaninaProductSortByEnum::NEWEST;
    public ?string $keyword;
    public ?string $categoryXid;
    public ?int $rating;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?string $merchantXid;
}
