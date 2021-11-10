<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondInvalidException extends ApiException
{
    protected $code = 'E_PFD_1';

    protected $message = 'Plafond Invalid';
}
