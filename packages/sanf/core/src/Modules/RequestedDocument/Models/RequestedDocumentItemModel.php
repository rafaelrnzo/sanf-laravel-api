<?php

namespace Sanf\Core\Modules\RequestedDocument\Models;

use NbsPhp\Core\Models\AbstractModel;

class RequestedDocumentItemModel extends AbstractModel
{

    protected $table = 'requested_document_item';
    protected $fillable = [
        'xid',
        'requested_document_id',
        'document_id',
        'document_name',
        'document_file',
        'deleted_at',
        'is_submitted',
    ];

    protected $casts = [
        'document_file' => 'object',
    ];
}
