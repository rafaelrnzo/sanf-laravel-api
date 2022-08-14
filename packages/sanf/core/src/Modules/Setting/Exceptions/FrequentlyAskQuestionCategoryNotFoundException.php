<?php

namespace Sanf\Core\Modules\Setting\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class FrequentlyAskQuestionCategoryNotFoundException extends ApiException
{
    protected $code = 'E_FAQ_2';
    protected $message = 'Data Not Found';
}