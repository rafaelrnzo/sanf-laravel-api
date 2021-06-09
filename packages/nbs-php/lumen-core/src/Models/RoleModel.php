<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use NbsPhp\Core\Traits\TimezoneMutable;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property object|array $permissions
 * @property-read object|array $permissions_key
 * @property-read string $permissions_value
 * @property Carbon|string|null $created_at
 * @property object|array $created_by
 * @property Carbon|string|null $updated_at
 * @property object|array $updated_by
 */
class RoleModel extends Model
{
    use TimezoneMutable;

    protected $fillable = [
        'name',
        'description',
        'permissions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    protected $timezoneable = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => 'Y-m-d H:i:s',
    ];

    protected $table = 'role';

    public function __construct(array $attributes = [])
    {
        $this->table = 'role';

        parent::__construct($attributes);
    }

    public function roleUsers()
    {
        return $this->hasMany(RoleUserModel::class, 'role_id', 'id');
    }

    public function getPermissionsKeyAttribute()
    {
        return collect($this->permissions)
            ->pluck('key')
            ->flatten()
            ->toArray();
    }

    public function getPermissionsValueAttribute()
    {
        return collect($this->permissions)
            ->pluck('value')
            ->join(',<br>');
    }
}
