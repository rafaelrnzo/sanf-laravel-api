<?php

namespace Sanf\Core\Modules\Financing\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class FinancingApplicationHistoryEncryptedModel extends FinancingApplicationHistoryModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    public $fillable = [
        'application_id',
        'status_id',
        'created_by',
        'method_id',
        'facility_id',
        'amount',
        'down_payment_amount',
        'down_payment_percentage',
        'tax_amount',
        'vat_amount',
        'backharge_amount',
        'other_amount',
        'total_amount',
        'tenor',
        'request_snapshot',
        'client',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function getCreatedByAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['created_by']);
    }
}
