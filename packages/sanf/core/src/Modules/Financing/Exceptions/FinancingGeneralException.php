<?php

namespace Sanf\Core\Modules\Financing\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FinancingGeneralException extends ApiException
{
    protected $code = 'E_FMT_1';

    protected $message = 'General Financing Error';
}
