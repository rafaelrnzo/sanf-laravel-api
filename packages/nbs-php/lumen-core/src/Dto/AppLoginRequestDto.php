<?php

namespace NbsPhp\Core\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class AppLoginRequestDto extends DataTransferObject
{
    public string $clientId;

    public string $clientSecret;
}
