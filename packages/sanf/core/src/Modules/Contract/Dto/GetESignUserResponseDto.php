<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Carbon\Carbon;
use Spatie\DataTransferObject\DataTransferObject;

class GetESignUserResponseDto extends DataTransferObject
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
    public ?int $provinceId;
    public ?int $districtId;
    public ?int $subDistrictId;
    public ?object $selfieFile;
    public ?object $identityFile;
    public int $statusId;
    public ?Carbon $createdAt;
    public ?Carbon $updatedAt;
}
