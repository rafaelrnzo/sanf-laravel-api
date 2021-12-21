<?php

namespace Sanf\Core\Modules\Contract\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FinancingUnitLocationSubmissionNotFoundException extends ApiException
{
    protected $code = 'E_FULS_2';

    protected $message = 'Financing Unit Location Submission Not Found';
}
