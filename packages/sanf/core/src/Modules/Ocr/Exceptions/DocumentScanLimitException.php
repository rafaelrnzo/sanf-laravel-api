<?php

namespace Sanf\Core\Modules\Ocr\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class DocumentScanLimitException extends ApiException
{
    protected $code = 'E_OCR_3';

    protected $message = 'Document scan limit';
}
