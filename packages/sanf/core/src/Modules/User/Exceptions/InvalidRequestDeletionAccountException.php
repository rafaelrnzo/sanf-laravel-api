<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InvalidRequestDeletionAccountException extends ApiException
{
    protected $code = 'E_ADEL_1';

    protected $message = "Invalid Delete Account";
}
