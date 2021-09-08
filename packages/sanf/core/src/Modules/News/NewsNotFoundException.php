<?php

namespace Sanf\Api\Modules\Promo;

use NbsPhp\Core\Exceptions\ApiException;

class NewsNotFoundException extends ApiException
{
    protected $code = 'NWS001';
    protected $message = 'News not found';
}