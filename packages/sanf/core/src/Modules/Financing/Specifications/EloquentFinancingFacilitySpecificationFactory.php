<?php


namespace Sanf\Core\Modules\Financing\Specifications;


class EloquentFinancingFacilitySpecificationFactory implements FinancingFacilitySpecificationFactoryInterface
{
    /**
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sort_by
     * @return EloquentPaginateFinancingFacilitySpecification
     */
    public function paginate(?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingFacilitySpecification($skip,$limit,$sort_by);
    }
}
