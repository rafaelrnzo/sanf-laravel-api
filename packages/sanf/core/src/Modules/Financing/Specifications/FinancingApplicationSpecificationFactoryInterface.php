<?php

namespace Sanf\Core\Modules\Financing\Specifications;

interface FinancingApplicationSpecificationFactoryInterface
{
    public function paginateByUser(int $userId, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword);
}
