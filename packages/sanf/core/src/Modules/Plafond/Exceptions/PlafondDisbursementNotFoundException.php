<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondDisbursementNotFoundException extends ApiException
{
    protected $code = 'E_PDB_1';

    protected $message = 'Plafond disbursement not found';
}
