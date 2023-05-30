<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductSparePartResponseDto extends FlexibleDataTransferObject
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
    public ?array $imageFile;
    public ?object $videoFile;
    public ?object $documentationFile;
    public ?int $priceBefore;
    public ?int $price;
    public ?int $year;
    public ?int $catalogId;
    public ?string $catalogName;
    public ?int $locationId;
    public ?string $locationName;
    public ?string $itemNumber;
    public ?string $weight;
    public ?string $length;
    public ?string $width;
    public ?string $height;
    public ?int $stock;
    public ?string $rating;
    public ?int $viewCount;
    public ?int $reviewCount;
    public ?int $itemSoldCount;
    public ?string $lastSeen;
    public ?int $createdAt;
    public ?int $updatedAt;
    public ?array $customerReviews;
}
