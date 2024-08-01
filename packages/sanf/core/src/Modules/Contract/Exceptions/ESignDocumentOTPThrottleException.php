<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentOTPThrottleException extends ApiException
{
    protected $code = 'E_ESIGN_4';

    protected $message = 'To many otp request';
}
