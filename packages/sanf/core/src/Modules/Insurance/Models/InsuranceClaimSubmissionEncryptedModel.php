<?php

namespace Sanf\Core\Modules\Insurance\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class InsuranceClaimSubmissionEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'insurance_claim_submission';

    protected $casts = [
        'image_files' => 'array',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function status()
    {
        return $this->belongsTo(InsuranceClaimSubmissionStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(InsuranceClaimSubmissionHistoryEncryptedModel::class, 'submission_id');
    }

    public function getLocationMetadataAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['location_metadata']));
    }
}
