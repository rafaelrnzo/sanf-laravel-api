<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ESignUserUniqueException extends ApiException
{
    protected $code = 'E_ESIGN_5';

    protected $message = 'Email and indentity number already exist';
}
