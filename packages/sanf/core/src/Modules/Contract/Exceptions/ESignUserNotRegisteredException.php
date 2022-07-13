<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignUserNotRegisteredException extends ApiException
{
    protected $code = 'E_ESIGN_1';

    protected $message = 'User Not Registered at TekenAja';
}
