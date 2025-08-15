<?php

namespace Sanf\Core\Modules\PdcHold\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

/**
 * @since CR2025
 */
class PdcHoldGiroNotFoundException extends ApiException
{
    protected $code = 'E_PDCHOLD_2';

    protected $message = 'Giro Hold not found';
}
