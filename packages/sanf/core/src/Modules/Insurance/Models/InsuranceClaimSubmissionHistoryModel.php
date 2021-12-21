<?php

namespace Sanf\Core\Modules\Insurance\Models;

use NbsPhp\Core\Models\AbstractModel;

class InsuranceClaimSubmissionHistoryModel extends AbstractModel
{
    public const UPDATED_AT = null;


    protected $table = 'insurance_claim_submission_history';

    protected $casts = [
        'created_by' => 'object'
    ];

    public function status()
    {
        return $this->belongsTo(InsuranceClaimSubmissionStatusModel::class, 'status_id');
    }
}
