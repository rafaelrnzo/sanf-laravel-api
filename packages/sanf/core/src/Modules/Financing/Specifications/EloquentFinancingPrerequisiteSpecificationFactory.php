<?php

namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingPrerequisiteSpecificationFactory implements FinancingPrerequisiteSpecificationFactoryInterface
{
    public function paginate(?int $skip = null, ?int $limit = null, ?string $sort_by = null)
    {
        return new EloquentPaginateFinancingPrerequisiteSpecification($skip, $limit, $sort_by);
    }
}
