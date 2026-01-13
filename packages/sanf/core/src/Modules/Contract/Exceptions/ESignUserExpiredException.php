<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignUserExpiredException extends ApiException
{
    protected $code = 'E_ESIGN_6';

    protected $message = 'User account expired';
}
