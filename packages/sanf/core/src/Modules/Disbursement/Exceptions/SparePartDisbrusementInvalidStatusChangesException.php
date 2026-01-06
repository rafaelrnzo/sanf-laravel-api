<?php

namespace Sanf\Core\Modules\Disbursement\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SparePartDisbrusementInvalidStatusChangesException extends ApiException
{
    protected $code = 'E_SPD_STATUS_CHANGES';
    protected $message = 'Spare Part disbursement status cannot be changed to the targetted status.';
}
