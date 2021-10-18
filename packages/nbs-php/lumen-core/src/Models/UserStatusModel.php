<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;

/**
 * @property int                $id
 * @property string             $name
 * @property-read string        $name_alt
 * @property Carbon|string|null $updated_at
 */
class UserStatusModel extends AbstractModel
{
    const STATUS_ACTIVE = 10;
    const STATUS_SUSPENDED = 20;

    protected $table = 'user_status';

    public function getNameAltAttribute()
    {
        return ucwords($this->name);
    }
}
