<?php

namespace Sanf\Integration\Enums;

class TekenAjaRegistrationErrorCodeEnum extends \MyCLabs\Enum\Enum
{
    public const SYSTEM_FAILURE = 'SYSTEM_FAILURE';
    public const EMAIL_EXISTS = 'EMAIL_EXISTS';
    public const USER_EXISTS = 'USER_EXISTS';
    public const SELFIE_UNMATCH = 'SELFIE_UNMATCH';
    public const BIODATA_UNMATCH = 'BIODATA_UNMATCH';
    public const INVALID_PARAMETER = 'INVALID_PARAMETER';
}