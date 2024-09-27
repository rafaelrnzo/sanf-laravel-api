<?php

namespace Sanf\Core\Modules\Insurance\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class InsuranceClaimSubmissionHistoryEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    public const UPDATED_AT = null;

    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'insurance_claim_submission_history';

    protected $hidden = [
        'nonce',
    ];

    public function status()
    {
        return $this->belongsTo(InsuranceClaimSubmissionStatusModel::class, 'status_id');
    }

    public function getCreatedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['created_by']));
    }
}
