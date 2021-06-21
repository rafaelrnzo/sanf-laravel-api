<?php


namespace NbsPhp\Core\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class SocialLoginDto extends DataTransferObject
{
    public string $fullName;

    public string $email;

    public ?string $phone;

    public string $providerId;

    public string $providerToken;

    public DeviceInfoRequestDto $device;
}
