<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

use NbsPhp\Core\Exceptions\ApiException;

class InvoiceCollectionSubmissionInvalidException extends ApiException
{
    protected $code = 'E_ICS_1';

    protected $message = 'Invoice Collection Submission Invalid';
}
