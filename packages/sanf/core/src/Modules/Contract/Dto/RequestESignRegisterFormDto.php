<?php

namespace Sanf\Core\Modules\Contract\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class RequestESignRegisterFormDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $sanfId;
    public string $email;
    public string $msisdn;
    public string $identityNo;
    public string $fullName;
    public string $dob;
    public string $pob;
    public int $gender;
    public string $address;
    public string $postalCode;
    public string $provinceId;
    public string $province;
    public string $cityId;
    public string $city;
    public string $district;
    public string $subDistrict;
    public string $selfieFile;
    public string $identityFile;
    public string $password;
    public string $passwordConfirmation;
}
