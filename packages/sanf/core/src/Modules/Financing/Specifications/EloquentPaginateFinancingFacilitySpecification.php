<?php

namespace Sanf\Core\Modules\Financing\Specifications;

use Sanf\Core\Modules\Financing\Models\FinancingFacilityModel;

class EloquentPaginateFinancingFacilitySpecification
{
    private ?int $skip;
    private ?int $limit;
    private ?string $sort_by;

    /**
     * EloquentFinancingMetadataSpecification constructor.
     * @param int $skip
     * @param int $limit
     * @param string $sort_by
     */
    public function __construct(?int $skip, ?int $limit, ?string $sort_by)
    {
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
