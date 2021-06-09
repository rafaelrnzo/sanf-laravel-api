<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;

/**
 * @property int                $id
 * @property string             $name
 * @property-read string        $name_alt
 * @property Carbon|string|null $updated_at
 */
class UserStatusAbstractModel extends AbstractModel
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    protected $table = 'user_status';

    public function getNameAltAttribute()
    {
        return ucwords($this->name);
    }
}
