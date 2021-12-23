<?php

namespace Sanf\Core\Modules\Invoice\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InvoiceCollectionSubmissionNotFoundException extends ApiException
{
    protected $code = 'E_ICS_2';

    protected $message = 'Invoice Collection Submission Not Found';
}
