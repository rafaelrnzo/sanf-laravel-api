<?php

namespace Sanf\Core\Modules\Invoice\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class InvoiceCollectionSubmissionHistoryEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    public const UPDATED_AT = null;

    protected $connection = ConnectionDB::PG_SQL;

    protected $table = 'invoice_collection_submission_history';

    protected $hidden = [
        'nonce',
    ];

    public function status()
    {
        return $this->belongsTo(InvoiceCollectionSubmissionStatusModel::class, 'status_id');
    }

    public function getCreatedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['created_by']));
    }
}
