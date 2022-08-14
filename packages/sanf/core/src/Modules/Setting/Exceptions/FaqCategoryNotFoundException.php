<?php

namespace Sanf\Core\Modules\Setting\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FaqCategoryNotFoundException extends ApiException
{
    protected $code = 'E_FAQC_1';
    protected $message = 'Data Not Found';
}