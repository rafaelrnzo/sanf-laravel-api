<?php


namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingMethodModel;

class EloquentPaginateFinancingMethodByFacilitySpecification
{
    private int $id;
    private ?int $skip;
    private ?int $limit;
    private ?string $sort_by;

    /**
     * EloquentPaginateFinancingMethodByFacilitySpecificationdSpecification constructor.
     * @param int $id
     * @param ?int $skip
     * @param ?int $limit
     * @param ?string $sort_by
     */
    public function __construct(int $id, ?int $skip, ?int $limit, ?string $sort_by)
    {
        $this->id = $id;
        $this->skip = $skip;
        $this->limit = $limit;
        $this->sort_by = $sort_by;
    }

    public function buildQuery(FinancingMethodModel $model)
    {
        $query = $model->newQuery()
            ->select([
                'financing_method.id',
                'financing_method.name',
                'financing_method.created_at',
            ])
            ->leftJoin('financing_facility_method', 'financing_facility_method.method_id', '=', 'financing_method.id')
            ->where('financing_facility_method.facility_id', $this->id)
            ->when($this->skip, function ($query) {
                return $query->skip($this->skip);
            })->when($this->limit, function ($query) {
                return $query->limit($this->limit);
            })->when($this->sort_by, function ($query) {
                return $query->orderBy('created_at', $this->sort_by);
            });
        return $query;
    }
}
