<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingStateHistoryModel extends Model
{
    public $timestamps = false;

    protected $table = 'standby_financing_state_histories';

    protected $fillable = [
        'application_id',
        'from_state',
        'to_state',
        'action',
        'notes',
        'actor_type',
        'actor_id',
        'actor_name',
        'created_at',
    ];
}
