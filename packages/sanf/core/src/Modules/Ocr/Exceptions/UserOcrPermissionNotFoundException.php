<?php

namespace Sanf\Core\Modules\Ocr\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class UserOcrPermissionNotFoundException extends ApiException
{
    protected $code = 'E_OCR_1';

    protected $message = 'User not found';
}
