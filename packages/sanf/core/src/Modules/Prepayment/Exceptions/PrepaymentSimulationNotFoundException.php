<?php

namespace Sanf\Core\Modules\Prepayment\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class PrepaymentSimulationNotFoundException extends ApiException
{
    protected $code = '001';

    protected $message = 'Prepayment Simulation Not Found Exception';
}
