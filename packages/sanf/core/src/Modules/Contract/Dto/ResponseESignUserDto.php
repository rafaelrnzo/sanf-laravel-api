<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class ResponseESignUserDto extends DataTransferObject
{
    public ?string $xid;
    public ?string $registrationId;
    public ?string $email;
    public ?string $msisdn;
    public ?string $nik;
    public ?string $fullName;
    public ?string $dob;
    public ?string $pob;
    public ?int $gender;
    public ?string $address;
    public ?string $postcode;
    public ?string $countryId;
    public ?string $countryName;
    public ?string $provinceId;
    public ?string $provinceName;
    public ?string $cityId;
    public ?string $cityName;
    public ?string $districtName;
    public ?string $subdistrictName;
    public ?object $selfieFile;
    public ?object $identityFile;
    public int $statusId;
    public bool $isAccountExpired;
}
