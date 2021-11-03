<?php


namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingMethodSpecificationFactory implements FinancingMethodSpecificationFactoryInterface
{
    /**
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sort_by
     * @return EloquentPaginateFinancingMethodSpecification
     */
    public function paginate(?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingMethodSpecification($skip, $limit, $sort_by);
    }

    /**
     * @param int $id
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sort_by
     * @return EloquentPaginateFinancingMethodByFacilitySpecification
     */
    public function paginateByFacility(int $id, ?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingMethodByFacilitySpecification($id, $skip, $limit, $sort_by);
    }
}
