<?php

namespace Sanf\Core\Modules\Project\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class InvalidStateProjectException extends ApiException
{
    protected $code = 'E_PROJ_2';

    protected $message = 'Invalid Project State';
}
