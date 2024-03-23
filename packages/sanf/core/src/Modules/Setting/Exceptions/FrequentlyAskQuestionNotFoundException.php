<?php

namespace Sanf\Core\Modules\Setting\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FrequentlyAskQuestionNotFoundException extends ApiException
{
    protected $code = 'E_FAQ_1';
    protected $message = 'Data Not Found';
}
