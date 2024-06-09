<?php

namespace Sanf\Core\Modules\Financing\Models;

use NbsPhp\Core\Models\AbstractModel;

class FinancingApplicationModel extends AbstractModel
{
    protected $table = 'financing_application';

    protected $casts = [
        'profile_snapshot' => 'object',
        'segment' => 'object',
    ];

    public function history()
    {
        return $this->belongsTo(FinancingApplicationHistoryModel::class, 'application_id');
    }

    public function facility()
    {
        return $this->belongsTo(FinancingFacilityModel::class, 'facility_id');
    }

    public function objects()
    {
        return $this->hasMany(FinancingObjectModel::class, 'application_id');
    }

    public function method()
    {
        return $this->belongsTo(FinancingMethodModel::class, 'method_id');
    }

    public function status()
    {
        return $this->belongsTo(FinancingStatusModel::class, 'status_id');
    }
}
