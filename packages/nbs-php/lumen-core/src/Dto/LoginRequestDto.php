<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class LoginRequestDto extends DataTransferObject
{
    public string $username;

    public string $password;

    public DeviceInfoRequestDto $device;
}
