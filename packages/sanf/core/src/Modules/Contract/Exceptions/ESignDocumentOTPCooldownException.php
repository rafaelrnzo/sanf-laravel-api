<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentOTPCooldownException extends ApiException
{
    protected $code = 'E_OTP_1';

    protected $message = 'To many otp request';
}
