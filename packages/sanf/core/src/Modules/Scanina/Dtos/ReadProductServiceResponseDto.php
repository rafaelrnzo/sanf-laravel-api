<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ReadProductServiceResponseDto extends FlexibleDataTransferObject
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
    public ?object $videoFile;
    public ?object $documentationFile;
    public ?int $priceBefore;
    public ?int $price;
    public ?int $year;
    public ?int $catalogId;
    public ?string $catalogName;
    public ?int $locationId;
    public ?string $location;
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
