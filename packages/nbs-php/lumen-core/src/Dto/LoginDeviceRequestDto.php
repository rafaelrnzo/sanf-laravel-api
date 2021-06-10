<?php


namespace NbsPhp\Core\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class LoginDeviceRequestDto extends DataTransferObject
{
    public $deviceId;
    public $devicePlatformId;
    public $notificationToken;
    public $notificationChannelId;
    public $metadata;
    public $userAgent;
}
