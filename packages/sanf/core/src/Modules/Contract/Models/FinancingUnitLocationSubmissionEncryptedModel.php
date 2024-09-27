<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Modules\User\AuthEncryptedModel;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class FinancingUnitLocationSubmissionEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'financing_unit_location_submission';

    protected $hidden = [
        'nonce',
    ];

    public function user()
    {
        return $this->belongsTo(AuthEncryptedModel::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(FinancingUnitLocationSubmissionStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(FinancingUnitLocationSubmissionHistoryEncryptedModel::class, 'submission_id');
    }

    public function getSubmittedLocationMetadataAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['submitted_location_metadata']);
    }

    public function getCityNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['city_name']);
    }
}
