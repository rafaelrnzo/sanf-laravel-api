<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingPlafondLockModel extends Model
{
    protected $table = 'standby_financing_plafond_locks';

    protected $fillable = [
        'application_id',
        'no_plafond',
        'locked_amount',
        'lock_status',
        'locked_at',
        'released_at',
        'used_at',
        'reason',
    ];

    protected $casts = [
        'locked_amount' => 'float',
        'locked_at' => 'datetime',
        'released_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
