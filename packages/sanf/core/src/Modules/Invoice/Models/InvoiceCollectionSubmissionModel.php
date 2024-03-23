<?php

namespace Sanf\Core\Modules\Invoice\Models;

use NbsPhp\Core\Models\AbstractModel;

class InvoiceCollectionSubmissionModel extends AbstractModel
{
    protected $table = 'invoice_collection_submission';

    public function status()
    {
        return $this->belongsTo(InvoiceCollectionSubmissionStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(InvoiceCollectionSubmissionHistoryModel::class, 'submission_id');
    }
}
