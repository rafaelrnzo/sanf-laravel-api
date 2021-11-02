<?php


namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingSpecificationFactory implements FinancingSpecificationFactoryInterface
{
    /**
     * @param int|null $skip
     * @param int|null $limit
     * @param string|null $sort_by
     * @return EloquentPaginateFinancingMethodSpecification
     */
    public function paginate(?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingMethodSpecification($skip,$limit,$sort_by);
    }

    public function paginateFinancingPrerequisite(?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingPrerequisiteSpecification($skip,$limit,$sort_by);
    }

    public function findById(?int $id)
    {
        return new EloquentFindByIdFinancingMethodSpecification($id);
    }
}
