<?php

namespace Sanf\Core\Modules\Financing\Specifications;

class EloquentFinancingApplicationSpecificationFactory implements FinancingApplicationSpecificationFactoryInterface
{
    public function paginateByUser(int $userId, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateFinancingApplicationByUserSpecification($userId, $skip, $limit, $sortBy, $keyword);
    }

    public function findByMonth(\DateTimeImmutable $dateTime)
    {
        return new EloquentFinancingApplicationByMonthSpecification($dateTime);
    }
}
