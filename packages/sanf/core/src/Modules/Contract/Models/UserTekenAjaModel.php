<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class UserTekenAjaModel extends AbstractModel
{
    protected $table = 'user_tekenaja';

    protected $fillable = [
        'email',
        'msisdn',
        'nik',
        'full_name',
        'dob',
        'pob',
        'gender',
        'address',
        'postal_code',
        'province_id',
        'district_id',
        'sub_district_id',
        'selfie_file',
        'identity_file',
        'total_submit_registration',
        'updated_at',
    ];

    protected $casts = [
        'selfie_file' => 'object',
        'identity_file' => 'object',
    ];
}
