<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondDisbursementSubmissionNotFoundException extends ApiException
{
    protected $code = 'E_PDBS_1';

    protected $message = 'Plafond disbursement submission not found';
}
