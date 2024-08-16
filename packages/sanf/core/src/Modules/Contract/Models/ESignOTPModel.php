<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class ESignOTPModel extends AbstractModel
{
    protected $table = 'esign_otp';

    protected $fillable = [
        'xid',
        'user_id',
        'sanf_id',
        'email',
        'msisdn',
        'expired_at',
        'cooldown_end_at',
        'suspend_end_at',
        'code',
        'reference_no',
        'transaction_no',
        'attempt',
        'created_at',
        'updated_at',
    ];
}
