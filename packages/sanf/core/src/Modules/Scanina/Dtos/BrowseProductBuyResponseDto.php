<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductBuyResponseDto extends FlexibleDataTransferObject
{
    public ?int $id;
    public ?string $xid;
    public ?int $shopId;
    public ?string $shopName;
    public ?int $merchantId;
    public ?string $merchantName;
    public ?string $serialNumber;
    public ?string $name;
    public ?string $slugName;
    public ?string $description;
    public ?array $imageFiles;
    public ?int $priceBefore;
    public ?int $price;
    public ?int $year;
    public ?int $catalogId;
    public ?string $catalogName;
    public ?object $unitMeasurement;
    public ?int $locationId;
    public ?string $location;
    public ?int $conditionTypeId;
    public ?string $conditionTypeName;
    public ?int $stock;
    public ?string $rating;
    public ?bool $isQualified;
    public ?bool $isAssurance;
    public ?string $latitude;
    public ?string $longitude;
    public ?int $createdAt;
    public ?int $updatedAt;
    public ?int $quantity;
}
