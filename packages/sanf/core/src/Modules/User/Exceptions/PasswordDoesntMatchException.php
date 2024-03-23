<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PasswordDoesntMatchException extends ApiException
{
    protected $code = 'E_PSW_1';
    protected $message = "Password Doesn't Match";
}
