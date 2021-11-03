<?php


namespace Sanf\Core\Modules\Financing\Specifications;


class EloquentFinancingMethodByFacilitySpecificationFactory implements FinancingMethodByFacilitySpecificationFactoryInterface
{
    /**
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sort_by
     * @return EloquentPaginateFinancingMethodByFacilitySpecification
     */
    public function paginate(?int $id, ?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingMethodByFacilitySpecification($id, $skip, $limit, $sort_by);
    }
}
