<?php


namespace Sanf\Core\Modules\Financing\Specifications;


use Sanf\Core\Modules\Financing\Models\FinancingMethodModel;

class EloquentFindByIdFinancingMethodSpecification
{
    private int $id;
    /**
     * EloquentFindByIdFinancingMethodSpecification constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id= $id;
    }

    public function buildQuery(FinancingMethodModel $model)
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'name',
                'interest_rate',
            ])
            ->where('id',$this->id);
        return $query;
    }

}
