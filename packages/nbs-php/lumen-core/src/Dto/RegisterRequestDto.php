<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class RegisterRequestDto extends DataTransferObject
{
    public string $fullName;

    public string $email;

    public ?string $landlineNumber;

    public string $phoneNumber;

    public string $password;
}
