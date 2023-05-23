<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductRentResponseDto extends FlexibleDataTransferObject
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
    public ?string $monthPrice;
    public ?string $dayPrice;
    public ?string $hourPrice;
    public ?string $startDateAvailable;
    public ?string $endDateAvailable;
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
    public ?string $viewCount;
    public ?string $lastSeen;
    public ?string $isQualified;
    public ?string $isAssurance;
    public ?string $latitude;
    public ?string $longitude;
    public ?string $createdAt;
    public ?string $updatedAt;
    public ?object $reviews;
    public ?array $technicalDetail;
    public ?array $specifications;
}
