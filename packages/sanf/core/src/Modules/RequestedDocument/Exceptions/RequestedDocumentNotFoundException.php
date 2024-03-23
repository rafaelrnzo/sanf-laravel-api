<?php

namespace Sanf\Core\Modules\RequestedDocument\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class RequestedDocumentNotFoundException extends ApiException
{
    protected $code = 'RD001';
    protected $message = 'Requested Document not found';
}
