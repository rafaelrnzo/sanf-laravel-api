<?php

namespace Sanf\Dashboard\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class AccountExistException extends ApiException
{
    protected $code = 'E_AUTH_2';

    protected $message = 'Account already exist';
}
