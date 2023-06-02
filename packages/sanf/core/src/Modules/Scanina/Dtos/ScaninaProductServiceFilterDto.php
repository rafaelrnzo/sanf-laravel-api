<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductServiceFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?int $categoryXid;
    public ?int $rating;
    public ?int $minPrice;
    public ?int $maxPrice;
    public ?int $merchantXid;
}
