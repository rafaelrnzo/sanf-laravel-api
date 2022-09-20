<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class RequestDeletionAccountNotFoundException extends ApiException
{
    protected $code = 'E_ADEL_2';

    protected $message = "User Delete Account Not Found";
}
