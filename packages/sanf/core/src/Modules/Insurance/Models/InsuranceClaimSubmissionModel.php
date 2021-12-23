<?php

namespace Sanf\Core\Modules\Insurance\Models;


use NbsPhp\Core\Models\AbstractModel;

class InsuranceClaimSubmissionModel extends AbstractModel
{
    protected $table = 'insurance_claim_submission';

    protected $casts = [
        'location_metadata' => 'object',
        'image_files' => 'array'
    ];

    public function status()
    {
        return $this->belongsTo(InsuranceClaimSubmissionStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(InsuranceClaimSubmissionHistoryModel::class, 'submission_id');
    }
}
