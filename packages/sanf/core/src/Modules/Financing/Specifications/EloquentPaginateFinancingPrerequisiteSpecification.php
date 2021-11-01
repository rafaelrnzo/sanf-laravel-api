<?php


namespace Sanf\Core\Modules\Financing\Specifications;


use Sanf\Core\Modules\Financing\Models\FinancingPrerequisiteModel;

class EloquentPaginateFinancingPrerequisiteSpecification
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

    public function buildQuery(FinancingPrerequisiteModel $model)
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'title',
                'description'
            ])
            ->with('items')
            ->whereNull('parent_id')
            ->limit($this->limit)
            ->offset($this->skip)
            ->orderBy('created_at', $this->sort_by);

        return $query;
    }

}
