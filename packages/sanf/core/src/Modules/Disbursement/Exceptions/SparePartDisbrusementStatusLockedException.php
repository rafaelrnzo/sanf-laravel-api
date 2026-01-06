<?php

namespace Sanf\Core\Modules\Disbursement\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SparePartDisbrusementStatusLockedException extends ApiException
{
    protected $code = 'E_SPD_STATUS_LOCKED';
    protected $message = 'Spare Part disbursement status cannot be changed while it is in the current status id.';
}
