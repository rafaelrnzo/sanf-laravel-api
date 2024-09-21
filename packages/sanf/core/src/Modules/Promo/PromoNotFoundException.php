<?php

namespace Sanf\Core\Modules\Promo;

use NbsPhp\Core\Exceptions\ApiException;

class PromoNotFoundException extends ApiException
{
    protected $code = 'PRMS001';
    protected $message = 'Promo SANFIND not found';
}
