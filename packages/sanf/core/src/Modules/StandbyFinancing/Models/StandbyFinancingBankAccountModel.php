<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingBankAccountModel extends Model
{
    protected $table = 'standby_financing_bank_accounts';

    protected $fillable = [
        'application_id',
        'bank_id',
        'owner',
        'provider',
        'account_number',
        'total_amount',
        'is_default',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];
}
