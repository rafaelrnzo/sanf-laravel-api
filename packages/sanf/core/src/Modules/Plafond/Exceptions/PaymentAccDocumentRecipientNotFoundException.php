<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PaymentAccDocumentRecipientNotFoundException extends ApiException
{
    protected $code = 'E_PFACD_2';

    protected $message = 'Recipient tidak ditemukan';
}
