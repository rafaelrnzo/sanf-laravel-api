<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseProductSparePartRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = 'latest';
    public ?string $keyword;
    public ?string $categoryXid;
    public ?string $brandXid;
    public ?int $rating;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?string $merchantXid;
}
