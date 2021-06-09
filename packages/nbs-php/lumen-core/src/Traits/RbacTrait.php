<?php

namespace NbsPhp\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use NbsPhp\Core\Models\RoleModel;
use NbsPhp\Core\Models\RoleUserModel;

/**
 * @property Collection $roles
 * @property-read RoleModel $role
 * @property-read int|null $role_id
 * @property-read string|null $role_name
 * @property-read string|null $role_description
 */
trait RbacTrait
{
    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(RoleModel::class, 'role_user', 'user_id', 'role_id', 'id')
            ->using(RoleUserModel::class)
            ->withPivot(['created_by']);
    }

    /**
     * @return RoleModel
     */
    public function getRoleAttribute()
    {
        return optional($this->roles)->first();
    }

    /**
     * @return int|null
     */
    public function getRoleIdAttribute()
    {
        return optional($this->role)->id;
    }

    /**
     * @return string|null
     */
    public function getRoleNameAttribute()
    {
        return optional($this->role)->name;
    }

    /**
     * @return string|null
     */
    public function getRoleDescriptionAttribute()
    {
        return optional($this->role)->description;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function hasRole(int $id): bool
    {
        foreach ($this->roles as $role) {
            if ($role->id === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|array $permission
     *
     * @return bool
     */
    public function hasAccess($permission)
    {
        if (is_null($this->roles)) {
            return false;
        }

        if (is_array($permission)) {
            foreach ($permission as $perm) {
                /** @var RoleModel $role */
                foreach ($this->roles as $role) {
                    if (in_array($perm, $role->permissions_key)) {
                        return true;
                    }
                }
            }
        } else {
            /** @var RoleModel $role */
            foreach ($this->roles as $role) {
                if (in_array($permission, $role->permissions_key)) {
                    return true;
                }
            }
        }

        return false;
    }
}
