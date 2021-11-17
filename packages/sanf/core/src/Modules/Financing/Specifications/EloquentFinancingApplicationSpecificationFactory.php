<?php

namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingApplicationSpecificationFactory implements FinancingApplicationSpecificationFactoryInterface
{
    public function paginateByUser(int $userId, ?int $skip = null, ?int $limit = null, ?string $sortBy = null, ?string $keyword = null)
    {
        return new EloquentPaginateFinancingApplicationByUserSpecification($userId, $skip, $limit, $sortBy, $keyword);
    }

    public function findByMonth(\DateTimeImmutable $dateTime)
    {
        return new EloquentFinancingApplicationByMonthSpecification($dateTime);
    }
}
