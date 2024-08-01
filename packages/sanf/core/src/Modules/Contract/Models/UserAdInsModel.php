<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserAdInsModel extends AbstractModel
{
    protected $table = 'user_adins';

    protected $fillable = [
        'xid',
        'user_id',
        'sanf_id',
        'email',
        'msisdn',
        'identity_no',
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'address',
        'postal_code',
        'province',
        'city',
        'district',
        'sub_district',
        'selfie_file',
        'identity_file',
        'status_id',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'selfie_file' => 'object',
        'identity_file' => 'object',
    ];
}
