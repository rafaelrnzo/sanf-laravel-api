<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $role_id
 * @property-read RoleModel $role
 * @property int $user_id
 * @property Carbon|string|null $created_at
 * @property object|array $created_by
 */
class RoleUserModel extends Pivot
{
    protected $table = 'role_user';

    public function __construct(array $attributes = [])
    {
        $this->table = 'role_user';

        parent::__construct($attributes);
    }


    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->created_at = Carbon::now();
        });
    }

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id', 'id');
    }
}
