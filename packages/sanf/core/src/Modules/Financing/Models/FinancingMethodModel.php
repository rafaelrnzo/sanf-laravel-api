<?php

namespace Sanf\Core\Modules\Financing\Models;

use NbsPhp\Core\Models\AbstractModel;

class FinancingMethodModel extends AbstractModel
{
    protected $table = 'financing_method';

    public function facilities()
    {
        return $this->belongsToMany(
            FinancingFacilityMethodModel::class,
            'financing_facility_method',
            'facility_id',
            'method_id'
        );
    }
}
