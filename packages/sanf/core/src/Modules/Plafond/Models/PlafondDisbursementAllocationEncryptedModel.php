<?php

namespace Sanf\Core\Modules\Plafond\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class PlafondDisbursementAllocationEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'plafond_disbursement_allocation';

    protected $fillable = [
        'xid',
        'plafond_disbursement_id',
        'submission_id',
        'bank_id',
        'owner',
        'provider',
        'account_no',
        'is_default',
        'amount',
        'notes',
        'order_no',
        'version',
        'created_at',
        'updated_at',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getOwnerAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['owner']);
    }

    public function getProviderAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['provider']);
    }

    public function getAccountNoAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['account_no']);
    }

    public function getNotesAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['notes']);
    }
}
