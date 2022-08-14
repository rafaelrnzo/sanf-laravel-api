<?php

namespace Sanf\Core\Modules\Setting\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OnBoardingNotFoundException extends ApiException
{
    protected $code = 'E_ONBRD_1';
    protected $message = 'Data Not Found';
}