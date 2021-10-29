<?php


namespace Sanf\Core\Modules\Financing\Models;


use NbsPhp\Core\Models\AbstractModel;

class FinancingPrerequisiteModel extends AbstractModel
{
    protected $table = 'financing_prerequisites';

    public function items(){
        return $this->hasMany(FinancingPrerequisiteModel::class, 'parent_id', 'id');
    }
}

