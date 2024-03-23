<?php

namespace Sanf\Core\Modules\Branch;

use NbsPhp\Core\Models\AbstractModel;

class BranchModel extends AbstractModel
{
    protected $table = 'branch';

    protected $fillable = [
        'name',
        'address',
        'msisdn',
        'msisdn_alternative',
        'email',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
        'modified_by',
    ];
}
