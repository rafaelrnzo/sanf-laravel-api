<?php


namespace Sanf\Core\Modules\Financing\Specifications;


class EloquentFinancingPrerequisiteSpecificationFactory implements FinancingPrerequisiteSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sort_by)
    {
        return new EloquentPaginateFinancingPrerequisiteSpecification($skip,$limit,$sort_by);
    }
}
