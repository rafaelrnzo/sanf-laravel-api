<?php

namespace Sanf\Core\Modules\Contract\Models;

use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\User\AuthModel;

class FinancingUnitLocationSubmissionModel extends AbstractModel
{
    protected $table = 'financing_unit_location_submission';

    public function user()
    {
        return $this->belongsTo(AuthModel::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(FinancingUnitLocationSubmissionStatusModel::class, 'status_id');
    }

    public function histories()
    {
        return $this->hasMany(FinancingUnitLocationSubmissionHistoryModel::class, 'submission_id');
    }
}
