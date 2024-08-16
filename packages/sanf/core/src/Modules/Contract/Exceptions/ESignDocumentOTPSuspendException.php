<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignDocumentOTPSuspendException extends ApiException
{
    protected $code = 'E_OTP_2';

    protected $message = 'Suspend request until available';
}
