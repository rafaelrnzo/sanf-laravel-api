<?php

namespace Sanf\Core\Modules\User\Exceptions;

use NbsPhp\Core\Exceptions\ApiException;

class OTPSuspendedException extends ApiException
{
    protected $message = 'Akun Anda ditangguhkan sementara karena terlalu banyak percobaan salah. Silakan coba lagi besok.';
    protected $status = 429;
}
