<?php

namespace Sanf\Core\Modules\Insurance\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InsuranceClaimSubmissionInvalidException extends ApiException
{
    protected $code = 'E_INSCS_1';

    protected $message = 'Insurance Claim Submission Invalid';
}
