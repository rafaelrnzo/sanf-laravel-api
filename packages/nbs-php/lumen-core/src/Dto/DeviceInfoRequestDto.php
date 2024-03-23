<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class DeviceInfoRequestDto extends DataTransferObject
{
    public $deviceId;

    public $devicePlatformId;

    public $notificationToken;

    public $notificationChannelId;

    public $metadata;
}
