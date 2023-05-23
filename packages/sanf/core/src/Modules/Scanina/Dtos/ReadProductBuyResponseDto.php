<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductBuyResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $xid;
    public ?string $shopId;
    public ?string $shopName;
    public ?string $merchantId;
    public ?string $merchantName;
    public ?string $serialNumber;
    public ?string $name;
    public ?string $slugName;
    public ?string $description;
    public ?array $imageFiles;
    public ?array $videoFiles;
    public ?array $documentationFiles;
    public ?string $priceBefore;
    public ?string $price;
    public ?string $year;
    public ?string $catalogId;
    public ?string $catalogName;
    public ?object $unitMeasurement;
    public ?string $locationId;
    public ?string $locationName;
    public ?string $conditionTypeId;
    public ?string $conditionTypeName;
    public ?string $itemNumber;
    public ?string $stock;
    public ?string $rating;
    public ?string $isQualified;
    public ?string $isAssurance;
    public ?string $latitude;
    public ?string $longitude;
    public ?string $createdAt;
    public ?string $updatedAt;
    public ?object $review;
    public ?array $specifications;
}
