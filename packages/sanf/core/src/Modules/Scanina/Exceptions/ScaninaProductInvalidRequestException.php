<?php

namespace Sanf\Core\Modules\Scanina\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ScaninaProductInvalidRequestException extends ApiException
{
    protected $code = 'E_SCAN_2';

    protected $message = 'Invalid request';
}
