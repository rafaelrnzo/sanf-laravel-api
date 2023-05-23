<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductSparePartResponseDto extends FlexibleDataTransferObject
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
    public ?object $videoFile;
    public ?object $documentationFile;
    public ?string $priceBefore;
    public ?string $price;
    public ?string $year;
    public ?string $catalogId;
    public ?string $catalogName;
    public ?string $locationId;
    public ?string $locationName;
    public ?string $itemNumber;
    public ?string $weight;
    public ?string $length;
    public ?string $width;
    public ?string $height;
    public ?string $stock;
    public ?string $rating;
    public ?string $viewCount;
    public ?string $reviewCount;
    public ?string $itemSoldCount;
    public ?string $lastSeen;
    public ?string $createdAt;
    public ?string $updatedAt;
    public ?array $customerReviews;
}
