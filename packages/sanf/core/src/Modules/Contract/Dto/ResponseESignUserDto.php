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
    public ?int $postalCode;
    public ?string $province;
    public ?string $city;
    public ?string $district;
    public ?string $subDistrict;
    public ?object $selfieFile;
    public ?object $identityFile;
    public int $statusId;
}
