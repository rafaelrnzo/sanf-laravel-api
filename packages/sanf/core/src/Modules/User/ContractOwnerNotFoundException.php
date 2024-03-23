<?php

namespace Sanf\Core\Modules\User;

use NbsPhp\Core\Exceptions\ApiException;

class ContractOwnerNotFoundException extends ApiException
{
    protected $code = 'E_USER_3';
    protected $message = 'User as Contract Owner Not Found';
}
