<?php

namespace Sanf\Core\Modules\Plafond\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PlafondDisbursementIsNotRevisionException extends ApiException
{
    protected $code = 'E_PDB_2';

    protected $message = 'Plafond disbursement is not revision';
}
