<?php

namespace Sanf\Core\Modules\Plafond\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class PlafondDisbursementEncryptedModel extends PlafondDisbursementModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'plafond_disbursement_encrypted';

    protected $hidden = [
        'nonce',
    ];

    protected $fillable = [
        'xid',
        'plafond_id',
        'disbursement_no',
        'client_id',
        'client_name',
        'client_mail',
        'customer_id',
        'customer_bowheer_id',
        'customer_name',
        'customer_mail',
        'customer_code',
        'customer_review',
        'status_id',
        'status',
        'client_amount',
        'customer_amount',
        'admin_amount',
        'plafond_submission_xid',
        'customer_updated_at',
        'admin_updated_at',
        'version',
        'created_at',
        'updated_at',
        'user_id',
        'nonce',
    ];

    public function disbursementRelation()
    {
        return $this->hasOne(PlafondDisbursementSubmissionEncryptedModel::class, 'xid', 'plafond_submission_xid');
    }

    public function getClientNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['client_name']);
    }

    public function getClientMailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['client_mail']);
    }

    public function getCustomerNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['customer_name']);
    }

    public function getCustomerMailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['customer_mail']);
    }
}
