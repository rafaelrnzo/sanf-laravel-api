<?php

namespace Sanf\Core\Modules\Insurance\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InsuranceClaimSubmissionNotFoundException extends ApiException
{
    protected $code = 'E_INSCS_2';

    protected $message = 'Insurance Claim Submission Not Found';
}
