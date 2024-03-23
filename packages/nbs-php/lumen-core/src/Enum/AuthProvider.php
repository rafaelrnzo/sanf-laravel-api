<?php

namespace NbsPhp\Core\Enum;

use MyCLabs\Enum\Enum;

/**
 * Class AuthProvider.
 */
class AuthProvider extends Enum
{
    const APP = 10;
    const GOOGLE = 20;
    const FACEBOOK = 30;
    const APPLE = 40;
}
