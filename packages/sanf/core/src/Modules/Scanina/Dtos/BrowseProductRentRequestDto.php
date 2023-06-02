<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseProductRentRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = 'latest';
    public ?string $keyword;
    public ?string $locationXid;
    public ?string $categoryXid;
    public ?string $brandXid;
    public ?string $modelXid;
    public ?string $startDate;
    public ?string $endDate;
    public ?bool $hasAssurance;
    public ?int $rating;
    public ?bool $isScanQualified;
    public ?int $status;
    public ?float $minPrice;
    public ?float $maxPrice;
    public ?int $minHourMeter;
    public ?int $condition;
    public ?string $merchantXid;
}
