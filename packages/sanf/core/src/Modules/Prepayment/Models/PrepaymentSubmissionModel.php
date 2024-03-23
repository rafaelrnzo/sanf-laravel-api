<?php

namespace Sanf\Core\Modules\Prepayment\Models;

use NbsPhp\Core\Models\AbstractModel;

class PrepaymentSubmissionModel extends AbstractModel
{
    protected $table = 'prepayment_submission';

    protected $casts = [
        'items' => 'array',
    ];

    public function status()
    {
        return $this->belongsTo(PrepaymentStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(PrepaymentSubmissionHistoryModel::class, 'submission_id');
    }
}
