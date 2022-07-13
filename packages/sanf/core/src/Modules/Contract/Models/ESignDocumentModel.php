<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class ESignDocumentModel extends AbstractModel
{
    protected $table = 'esign_document';

    protected $fillable = [
        'document_file',
        'status_id',
        'updated_at',
        'modified_by',
    ];

    protected $casts = [
        'document_file' => 'object',
        'modified_by' => 'object',
    ];
}
