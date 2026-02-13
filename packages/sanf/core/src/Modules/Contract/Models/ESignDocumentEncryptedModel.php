<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Constants\ConnectionDB;
use Sanf\Core\Traits\SodiumEncryptionTrait;

class ESignDocumentEncryptedModel extends AbstractModel
{
    use SodiumEncryptionTrait;

    protected $connection = ConnectionDB::PG_SODIUM;

    protected $table = 'esign_document_encrypted';

    protected $fillable = [
        'xid',
        'document_id',
        'document_name',
        'document_file',
        'reference_no',
        'status_id',
        'version',
        'created_at',
        'updated_at',
        'expired_at',
        'modified_by',
        'nonce',
        'category_id',
    ];

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

    public function getModifiedByAttribute()
    {
        return json_decode($this->decryptor()->decrypt($this->attributes['modified_by']));
    }

    public function getReferenceNoAttribute()
    {
        return $this->decryptor()->decrypt($this->attributes['reference_no']);
    }

    public function eSignDocumentAssignees()
    {
        return $this->hasMany(ESignDocumentAssigneeEncryptedModel::class, 'document_id', 'document_id');
    }
}
