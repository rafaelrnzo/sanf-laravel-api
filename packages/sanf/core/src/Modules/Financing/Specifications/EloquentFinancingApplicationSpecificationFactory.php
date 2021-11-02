<?php

namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingApplicationSpecificationFactory implements FinancingApplicationSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateFinancingApplicationSpecification($skip, $limit, $sortBy, $keyword);
    }
}
