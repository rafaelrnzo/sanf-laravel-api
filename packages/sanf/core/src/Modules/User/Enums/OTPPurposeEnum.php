<?php

namespace Sanf\Core\Modules\User\Enums;

use MyCLabs\Enum\Enum;

class OTPPurposeEnum extends Enum
{
    public const LOGIN = 'login';
    public const CHANGE_PASSWORD = 'change_password';
    public const CHANGE_PIN = 'change_pin';
    public const RESET_PASSWORD = 'reset_password';
    public const RESET_PIN = 'reset_pin';
    public const REGISTRATION = 'registration';

    public const ALL = [
        self::LOGIN,
        self::CHANGE_PASSWORD,
        self::CHANGE_PIN,
        self::RESET_PASSWORD,
        self::RESET_PIN,
        self::REGISTRATION,
    ];
}
