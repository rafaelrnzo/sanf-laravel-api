<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductRentFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?string $locationId;
    public ?int $categoryId;
    public ?int $brandId;
    public ?int $modelId;
    public ?string $startRentalDate;
    public ?string $endRentalDate;
    public ?bool $assurance;
    public ?int $rating;
    public ?bool $scanQualified;
    public ?int $status;
    public ?int $minPrice;
    public ?int $maxPrice;
    public ?int $minHourMeter;
    public ?int $condition;
    public ?int $merchantId;
}
