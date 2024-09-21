<?php

namespace Sanf\Core\Modules\News;

use NbsPhp\Core\Exceptions\ApiException;

class NewsNotFoundException extends ApiException
{
    protected $code = 'NWS001';
    protected $message = 'News not found';
}
