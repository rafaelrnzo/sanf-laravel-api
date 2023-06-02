<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseMerchantResponseDto extends ScaninaRequestDataTransferObject
{
    public ?int $id;
    public ?string $xid;
    public ?int $shopId;
    public ?string $shopName;
    public ?string $companyName;
    public ?string $companyEmail;
    public ?string $companyPhoneNumber;
    public ?string $picName;
    public ?string $picEmail;
    public ?string $picPhoneNumber;
    public ?int $businessTypeId;
    public ?string $businessTypeName;
    public ?int $businessSectorId;
    public ?string $businessSectorName;
    public ?string $addressLocation;
    public ?string $addressCityName;
    public ?string $fullAddress;
    public ?string $postalCode;
    public ?string $latitude;
    public ?string $longitude;
    public ?int $createdAt;
    public ?int $updatedAt;
}
