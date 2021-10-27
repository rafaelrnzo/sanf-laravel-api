<?php


namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingMethodModel;

class EloquentPaginateFinancingMethodSpecification
{
    private int $skip;
    private int $limit;
    private string $sort_by;

    /**
     * EloquentFinancingMetadataSpecification constructor.
     * @param int $skip
     * @param int $limit
     * @param string $sort_by
     */
    public function __construct(int $skip, int $limit, string $sort_by)
    {
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sort_by = $sort_by;
    }

    public function buildQuery(FinancingMethodModel $model)
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'name',
            ])
            ->limit($this->limit)
            ->offset($this->skip)
            ->orderBy('created_at', $this->sort_by);
        return $query;
    }
}
