<?php

namespace Sanf\Core\Modules\User;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;

class UserAuthLogModel extends AbstractModel
{
    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'user_auth_log';

    protected $fillable = [
        'notes',
    ];

    protected $casts = [
        'created_by',
    ];
}
