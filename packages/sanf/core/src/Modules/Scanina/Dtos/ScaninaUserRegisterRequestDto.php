<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaUserRegisterRequestDto extends ScaninaFilterDataTransferObject
{
    public int $accountTypeId;
    public string $email;
    public string $fullName;
    public string $phoneNumber;
    public ?string $picName;
    public ?string $picPhoneNumber;
    public ?string $position;
    public string $countryId;
    public string $cityId;
    public string $businessSectorId;
    public string $password;
    public string $passwordConfirmation;
}
