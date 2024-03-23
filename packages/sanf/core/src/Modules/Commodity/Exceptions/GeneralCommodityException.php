<?php

namespace Sanf\Core\Modules\Commodity\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class GeneralCommodityException extends ApiException
{
    protected $code = 'E_COMD_1';

    protected $message = 'General Commodity Error';
}
