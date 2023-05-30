<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductRentResponseDto extends FlexibleDataTransferObject
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
    public ?string $monthPrice;
    public ?string $dayPrice;
    public ?string $hourPrice;
    public ?string $startDateAvailable;
    public ?string $endDateAvailable;
    public ?int $priceBefore;
    public ?int $price;
    public ?int $year;
    public ?int $catalogId;
    public ?string $catalogName;
    public ?object $unitMeasurement;
    public ?int $locationId;
    public ?string $locationName;
    public ?int $conditionTypeId;
    public ?string $conditionTypeName;
    public ?string $itemNumber;
    public ?int $stock;
    public ?string $rating;
    public ?int $viewCount;
    public ?string $lastSeen;
    public ?bool $isQualified;
    public ?bool $isAssurance;
    public ?string $latitude;
    public ?string $longitude;
    public ?int $createdAt;
    public ?int $updatedAt;
    public ?object $reviews;
    public ?array $technicalDetails;
    public ?array $subSpecifications;
}
