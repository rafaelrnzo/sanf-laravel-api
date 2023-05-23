<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ProductServiceResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $xid;
    public ?string $shopId;
    public ?string $shopName;
    public ?string $merchantId;
    public ?string $merchantName;
    public ?string $name;
    public ?string $slugName;
    public ?string $description;
    public ?object $imageFiles;
    public ?string $priceBefore;
    public ?string $price;
    public ?string $year;
    public ?string $catalogId;
    public ?string $catalogName;
    public ?object $unitMeasurement;
    public ?string $locationId;
    public ?string $locationName;
    public ?string $stock;
    public ?string $rating;
    public ?string $reviewCount;
    public ?string $itemSoldCount;
    public ?string $createdAt;
    public ?string $updatedAt;
}
