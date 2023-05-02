<?php

namespace Sanf\Core\Modules\RequestedDocument\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SubmitRequestedDocumentNotCompleteException extends ApiException
{

    protected $code = 'RD004';
    protected $message = 'Uploaded document not complete';
}