<?php

namespace Sanf\Integration\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class TekenAjaDocumentException extends ApiException
{
    protected $code = 'E_TEKEN_6';

    protected $message = 'Document Exception';
}
