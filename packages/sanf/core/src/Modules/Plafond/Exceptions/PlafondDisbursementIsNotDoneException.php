<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondDisbursementIsNotDoneException extends ApiException
{
    protected $code = 'E_PDB_3';

    protected $message = 'Plafond disbursement is not submitted to Admin SANFIND';
}
