<?php

namespace Sanf\Integration\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class TekenAjaSubmitRegistrationHasLimitException extends ApiException
{
    protected $code = 'E_TEKEN_5';

    protected $message = "There's no Quota";
}
