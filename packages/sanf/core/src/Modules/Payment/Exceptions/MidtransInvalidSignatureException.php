<?php

namespace Sanf\Core\Modules\Payment\Exceptions;

use Illuminate\Http\Response;
use NbsPhp\Core\Exceptions\ApiException;

class MidtransInvalidSignatureException extends ApiException
{
    protected $code = 'MIDTRANS_INVALID_SIGNATURE';
    protected $message = 'Invalid Midtrans signature';
    protected $status = Response::HTTP_BAD_REQUEST;
}
