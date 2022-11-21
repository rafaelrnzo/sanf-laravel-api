<?php

namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingCategorySpecificationFactory implements FinancingCategorySpecificationFactoryInterface
{
    public function paginate(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy)
    {
        return new EloquentPaginateFinancingCategorySpecification($keyword, $skip, $limit, $sortBy);
    }
}
