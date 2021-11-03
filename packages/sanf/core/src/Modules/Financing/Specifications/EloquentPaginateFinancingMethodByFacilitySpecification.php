<?php


namespace Sanf\Core\Modules\Financing\Specifications;


use Sanf\Core\Modules\Financing\Models\FinancingFacilityModel;

class EloquentPaginateFinancingMethodByFacilitySpecification
{
    private int $id;
    private int $skip;
    private int $limit;
    private string $sort_by;

    /**
     * EloquentFinancingMetadataSpecification constructor.
     * @param int $id
     * @param int $skip
     * @param int $limit
     * @param string $sort_by
     */
    public function __construct(int $id, int $skip, int $limit, string $sort_by)
    {
        $this->id = $id;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sort_by = $sort_by;
    }

    public function buildQuery(FinancingFacilityModel $model)
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'name',
            ])
            ->with(['methods' => function ($query) {
                return $query->select('id', 'name', 'financing_facility_method.created_at')
                    ->limit($this->limit)
                    ->offset($this->skip)
                    ->orderBy('financing_facility_method.created_at', $this->sort_by);
            }])
            ->where('id',$this->id);

        return $query;
    }
}
