<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class PostUserRegisterRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public string $xid;
    public string $name;
    public string $msisdn;
    public string $phoneNumber;
    public ?string $position;
    public string $businessSectorId;
    public int $countryId;
    public string $countryName;
    public ?int $cityId;
    public string $cityName;
    public string $password;
    public string $passwordConfirmation;
}
