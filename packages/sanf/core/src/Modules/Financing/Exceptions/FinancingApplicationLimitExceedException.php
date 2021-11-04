<?php

namespace Sanf\Core\Modules\Financing\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FinancingApplicationLimitExceedException extends ApiException
{
    protected $code = 'E_FAP_2';

    protected $message = 'Financing Application Limit Reached in a Month';
}
