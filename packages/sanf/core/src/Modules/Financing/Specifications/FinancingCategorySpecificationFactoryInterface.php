<?php

namespace Sanf\Core\Modules\Financing\Specifications;

interface FinancingCategorySpecificationFactoryInterface
{
    public function paginate(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy);
}