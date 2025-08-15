<?php

namespace Sanf\Core\Modules\PdcHold\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

/**
 * @since CR2025
 */
class PdcHoldNotFoundException extends ApiException
{
    protected $code = 'E_PDCHOLD_1';

    protected $message = 'PDC Hold not found';
}
