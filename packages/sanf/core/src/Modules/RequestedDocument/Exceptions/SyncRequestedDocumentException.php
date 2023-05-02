<?php

namespace Sanf\Core\Modules\RequestedDocument\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SyncRequestedDocumentException extends ApiException
{

    protected $code = 'RD005';
    protected $message = 'Requested Document totally not sync';
}