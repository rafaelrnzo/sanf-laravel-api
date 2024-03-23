<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class UpdateSessionRequestDto extends DataTransferObject
{
    public string $refreshToken;

    public DeviceInfoRequestDto $device;
}
