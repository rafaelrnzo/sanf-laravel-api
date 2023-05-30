<?php

namespace Sanf\Core\Modules\Scanina\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class ScaninaProductNotFoundException extends ApiException
{
    protected $code = 'E_SCAN_1';

    protected $message = 'Product Not Found';
}
