<?php

namespace Sanf\Integration\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SanfInternalApiDataNotFoundException extends ApiException
{
    protected $code = 'E_SANF_2';
}
