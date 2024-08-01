<?php

namespace Sanf\Integration\Modules\AdIns\DTOs;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class RegistrationDto extends CamelCaseDataTransferObject
{
    public ?string $fullName;
    public ?string $email;
    public ?string $dateOfBirth;
    public ?string $placeOfBirth;
    public ?string $gender;
    public ?string $msisdn;
    public ?string $identityNumber;
    public ?string $address;
    public ?string $province;
    public ?string $city;
    public ?string $district;
    public ?string $subDistrict;
    public ?string $postalCode;
    public ?string $selfPhoto;
    public ?string $identityCardPhoto;
    public ?string $password;
}
