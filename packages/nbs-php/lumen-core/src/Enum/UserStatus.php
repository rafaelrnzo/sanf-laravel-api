<?php


namespace NbsPhp\Core\Enum;


use MyCLabs\Enum\Enum;

/**
 * Class UserStatus
 * @package NbsPhp\Core\Enum
 */
class UserStatus extends Enum
{
    const ACTIVE = 10;
    const INACTIVE = 20;
    const NEED_ACTIVATION = 30;
}
