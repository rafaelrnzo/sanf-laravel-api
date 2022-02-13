<?php

namespace Sanf\Core\Modules\User\Dtos;

use Carbon\CarbonImmutable;
use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class MyProfileDto extends CamelCaseDataTransferObject
{
    public ?string $xid;
    public ?string $customerId;
    public ?string $typeId;
    public ?string $typeName;
    public ?string $title;
    public ?string $fullName;
    public ?string $picName;
    public ?string $identityNumber;
    public ?string $npwp;
    public ?string $email;
    public ?string $landlineNumber;
    public ?string $phoneNumber;
    public ?string $gender;
    public ?CarbonImmutable $birthdate;
    public ?string $countryId;
    public ?string $countryName;
    public ?string $provinceId;
    public ?string $provinceName;
    public ?string $cityId;
    public ?string $cityName;
    public ?string $districtName;
    public ?string $subdistrictName;
    public ?string $postcode;
    public ?string $address;
    public ?string $businessSince;
    public bool $isPic;
}
