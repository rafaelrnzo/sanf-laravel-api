<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductBuyFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?string $locationXid;
    public ?string $categoryXid;
    public ?string $brandXid;
    public ?string $typeXid;
    public ?string $modelXid;
    public ?int $minYear;
    public ?int $maxYear;
    public ?bool $assurance;
    public ?int $rating;
    public ?bool $scanQualified;
    public ?int $status;
    public ?int $minPrice;
    public ?int $maxPrice;
    public ?int $minHourMeter;
    public ?int $maxHourMeter;
    public ?int $condition;
    public ?string $merchantXid;
}
