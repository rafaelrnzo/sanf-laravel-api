<?php

namespace Sanf\Core\Modules\Financing\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class FinancingApplicationEncryptedModel extends FinancingApplicationModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $casts = [
        'segment' => 'object',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function history()
    {
        return $this->belongsTo(FinancingApplicationHistoryEncryptedModel::class, 'application_id');
    }

    public function getProfileSnapshotAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['profile_snapshot']));
    }
}
