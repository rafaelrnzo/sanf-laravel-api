<?php

namespace Sanf\Core\Modules\Financing\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FinancingApplicationInvalidException extends ApiException
{
    protected $code = 'E_FAP_1';

    protected $message = 'Financing Application Invalid';
}
