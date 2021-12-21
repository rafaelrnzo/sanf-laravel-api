<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;

class FinancingUnitLocationSubmissionHistoryModel extends AbstractModel
{
    public const UPDATED_AT = null;


    protected $table = 'financing_unit_location_submission_history';

    protected $casts = [
        'created_by' => 'object'
    ];

    public function status()
    {
        return $this->belongsTo(FinancingUnitLocationSubmissionStatusModel::class, 'status_id');
    }
}
