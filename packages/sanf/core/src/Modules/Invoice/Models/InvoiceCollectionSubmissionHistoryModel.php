<?php

namespace Sanf\Core\Modules\Invoice\Models;

use NbsPhp\Core\Models\AbstractModel;

class InvoiceCollectionSubmissionHistoryModel extends AbstractModel
{
    public const UPDATED_AT = null;

    protected $table = 'invoice_collection_submission_history';

    protected $casts = [
        'created_by' => 'object',
    ];

    public function status()
    {
        return $this->belongsTo(InvoiceCollectionSubmissionStatusModel::class, 'status_id');
    }
}
