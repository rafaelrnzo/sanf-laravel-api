<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondDisbursementHasDoneException extends ApiException
{
    protected $code = 'E_PDB_4';

    protected $message = 'Plafond disbursement already procesed by Admin SANFIND';
}
