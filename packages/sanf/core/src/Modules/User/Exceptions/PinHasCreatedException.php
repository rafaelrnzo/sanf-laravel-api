<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PinHasCreatedException extends ApiException
{
    protected $code = 'E_PIN_1';

    protected $message = 'User Pin Has Been Created';
}
