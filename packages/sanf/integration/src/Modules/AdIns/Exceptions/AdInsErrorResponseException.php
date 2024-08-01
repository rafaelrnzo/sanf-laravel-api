<?php

namespace Sanf\Integration\Modules\AdIns\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class AdInsErrorResponseException extends ApiException
{
    protected $code = 'E_ADINS_1';

    protected $message = 'Undefined error Ad-Ins integration';
}
