<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FinancingUnitLocationSubmissionInvalidException extends ApiException
{
    protected $code = 'E_FULS_1';

    protected $message = 'Financing Unit Location Submission Invalid';
}
