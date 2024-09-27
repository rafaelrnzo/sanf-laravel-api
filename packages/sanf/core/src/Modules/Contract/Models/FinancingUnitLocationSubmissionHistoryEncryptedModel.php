<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class FinancingUnitLocationSubmissionHistoryEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    public const UPDATED_AT = null;

    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'financing_unit_location_submission_history';

    protected $hidden = [
        'created_by',
    ];

    public function status()
    {
        return $this->belongsTo(FinancingUnitLocationSubmissionStatusModel::class, 'status_id');
    }

    public function getCreatedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['created_by']));
    }
}
