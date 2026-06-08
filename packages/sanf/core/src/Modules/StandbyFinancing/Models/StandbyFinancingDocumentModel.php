<?php

namespace Sanf\Core\Modules\StandbyFinancing\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyFinancingDocumentModel extends Model
{
    protected $table = 'standby_financing_documents';

    protected $fillable = [
        'application_id',
        'doc_id',
        'doc_desc',
        'file_path',
        'file_name',
        'required',
        'status',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];
}
