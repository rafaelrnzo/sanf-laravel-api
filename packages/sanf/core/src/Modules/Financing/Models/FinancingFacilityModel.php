<?php

namespace Sanf\Core\Modules\Financing\Models;

use NbsPhp\Core\Models\AbstractModel;

class FinancingFacilityModel extends AbstractModel
{
    protected $table = 'financing_facility';

    public function methods()
    {
        return $this->belongsToMany(
            FinancingMethodModel::class,
            'financing_facility_method',
            'facility_id',
            'method_id'
        );
    }
}
