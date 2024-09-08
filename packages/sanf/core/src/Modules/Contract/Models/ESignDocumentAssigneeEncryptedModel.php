<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class ESignDocumentAssigneeEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'esign_document_assignee_encrypted';

    protected $fillable = [
        'document_sign_url',
        'status_id',
        'updated_at',
        'nonce',
    ];

    protected $hidden = [
        'nonce',
    ];

    public function eSignDocument()
    {
        return $this->hasOne(ESignDocumentEncryptedModel::class, 'document_id', 'document_id');
    }

    public function getEmailAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['email']);
    }

    public function getDocumentSignUrlAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['document_sign_url']);
    }

    public function getReferenceNoAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['reference_no']);
    }
}
