<?php

namespace Sanf\Core\Modules\RequestedDocument\Models;

use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class RequestedDocumentItemEncryptedModel extends RequestedDocumentItemModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SQL;

    protected $fillable = [
        'xid',
        'requested_document_id',
        'document_id',
        'document_name',
        'document_file',
        'deleted_at',
        'is_submitted',
        'nonce',
    ];

    protected $casts = [];

    protected $hidden = [
        'nonce',
    ];

    public function getDocumentNameAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['document_name']);
    }

    public function getDocumentFileAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['document_file']));
    }
}
