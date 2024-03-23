<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PinResetInvalidException extends ApiException
{
    protected $code = 'E_PIN_6';

    protected $message = 'Invalid Reset Pin';
}
