<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PinResetCodeNotMatchException extends ApiException
{
    protected $code = 'E_PIN_4';

    protected $message = "Reset Pin Code Doesn't Match";
}
