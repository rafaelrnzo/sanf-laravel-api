<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductRentFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?string $locationId;
    public ?string $categoryXid;
    public ?string $brandXid;
    public ?string $modelXid;
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
    public ?string $merchantXid;
}
