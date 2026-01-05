<?php

namespace Sanf\Core\Modules\Disbursement\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class SparePartDisbrusementValidated extends ApiException
{
    protected $code = 'E_SPD_VALIDATED';
    protected $message = 'Spare Part disbursement already validated.';
}
