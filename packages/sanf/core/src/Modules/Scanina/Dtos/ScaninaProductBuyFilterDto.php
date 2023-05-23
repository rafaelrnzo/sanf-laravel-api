<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductBuyFilterDto extends ScaninaFilterDataTransferObject
{
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?string $locationId;
    public ?int $categoryId;
    public ?int $brandId;
    public ?int $typeId;
    public ?int $modelId;
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
    public ?int $merchantId;
}
