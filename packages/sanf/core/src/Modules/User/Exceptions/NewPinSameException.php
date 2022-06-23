<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class NewPinSameException extends ApiException
{
    protected $code = 'E_PIN_3';

    protected $message = "Pin can't be the same";
}
