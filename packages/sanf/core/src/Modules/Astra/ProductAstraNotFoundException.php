<?php

namespace Sanf\Core\Modules\Astra;

use NbsPhp\Core\Exceptions\ApiException;

class ProductAstraNotFoundException extends ApiException
{
    protected $code = 'PRMS001';
    protected $message = 'Promo Astra not found';
}
