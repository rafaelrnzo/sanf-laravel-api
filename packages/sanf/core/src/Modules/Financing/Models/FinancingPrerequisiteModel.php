<?php


namespace Sanf\Core\Modules\Financing\Models;


use NbsPhp\Core\Models\AbstractModel;

class FinancingPrerequisiteModel extends AbstractModel
{
    protected $table = 'financing_prerequisite';

    public function childs()
    {
        return $this->hasMany(FinancingPrerequisiteModel::class, 'parent_id', 'id');
    }

    public function items()
    {
        return $this->childs()->with('items');
    }
}

