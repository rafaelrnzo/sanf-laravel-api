<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPRateLimitedException extends ApiException
{
    protected $status = 429;

    public function __construct(int $minutes)
    {
        parent::__construct("Terlalu banyak permintaan. Silakan coba lagi dalam {$minutes} menit.");
    }
}
