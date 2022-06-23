<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PinDoesntMatchException extends ApiException
{
    protected $code = 'E_PIN_2';

    protected $message = "User Pin Doesn't Match";
}
