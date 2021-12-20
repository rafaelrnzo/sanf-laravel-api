<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

use NbsPhp\Core\Exceptions\ApiException;

class InvoiceCollectionSubmissionNotFoundException extends ApiException
{
    protected $code = 'E_ICS_2';

    protected $message = 'Invoice Collection Submission Not Found';
}
