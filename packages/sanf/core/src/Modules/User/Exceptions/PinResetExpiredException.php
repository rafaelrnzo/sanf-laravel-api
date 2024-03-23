<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PinResetExpiredException extends ApiException
{
    protected $code = 'E_PIN_5';

    protected $message = 'Reset Pin Code Was Expired';
}
