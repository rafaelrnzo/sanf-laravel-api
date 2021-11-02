<?php

namespace Sanf\Core\Modules\Financing\Specifications;

interface FinancingApplicationSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?string $keyword);
}
