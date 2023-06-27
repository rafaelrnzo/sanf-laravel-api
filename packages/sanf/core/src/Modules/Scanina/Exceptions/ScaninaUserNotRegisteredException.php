<?php

namespace Sanf\Core\Modules\Scanina\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ScaninaUserNotRegisteredException extends ApiException
{
    protected $code = 'E_ACCOUNT_2';

    protected $message = 'Account Not Registered';
}
