<?php

namespace Sanf\Core\Modules\Contract\Dto;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddESignUserDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $email;
    public string $msisdn;
    public string $nik;
    public string $fullName;
    public string $dob;
    public string $pob;
    public int $gender;
    public string $address;
    public int $postalCode;
    public int $provinceId;
    public int $districtId;
    public int $subDistrictId;
    public ?string $selfieFile;
    public ?string $identityFile;
}
