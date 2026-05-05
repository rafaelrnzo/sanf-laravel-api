<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPInvalidCodeException extends ApiException
{
    protected $message = 'Invalid OTP code';
    protected $status = 400;
}
