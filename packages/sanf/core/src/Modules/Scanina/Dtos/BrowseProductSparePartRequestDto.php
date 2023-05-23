<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseProductSparePartRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = 'latest';
    public ?string $keyword;
    public ?int $categoryId;
    public ?int $brandId;
    public ?int $rating;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?int $merchantId;
}
