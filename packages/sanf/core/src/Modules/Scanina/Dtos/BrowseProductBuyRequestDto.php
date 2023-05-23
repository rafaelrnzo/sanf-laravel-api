<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseProductBuyRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = 'latest';
    public ?string $keyword;
    public ?string $locationId;
    public ?int $categoryId;
    public ?int $brandId;
    public ?int $typeId;
    public ?int $modelId;
    public ?int $minYear;
    public ?int $maxYear;
    public ?bool $hasAssurance;
    public ?int $rating;
    public ?bool $isScanQualified;
    public ?int $status;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?int $minHourMeter;
    public ?int $maxHourMeter;
    public ?int $condition;
    public ?int $merchantId;
}
