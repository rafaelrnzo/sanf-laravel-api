<?php

namespace Sanf\Core\Modules\RequestedDocument\Models;

use NbsPhp\Core\Models\AbstractModel;

class RequestedDocumentModel extends AbstractModel
{

    protected $table = 'requested_document';
    protected $fillable = [
        'xid',
        'user_id',
        'request_no',
        'request_at',
        'document_no',
        'type',
        'total_item',
        'total_uploaded',
        'status',
        'deleted_at',
    ];

    public function items()
    {
        return $this->hasMany(RequestedDocumentItemModel::class, 'requested_document_id', 'id');
    }
}
