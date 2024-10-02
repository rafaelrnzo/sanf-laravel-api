<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class SocialLoginRequestDto extends DataTransferObject
{
    public string $providerToken;

    public DeviceInfoRequestDto $device;

    public string $email = null;
}
