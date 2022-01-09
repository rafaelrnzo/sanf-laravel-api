<?php

namespace Sanf\Core\Modules\Financing\Specifications;

interface FinancingApplicationSpecificationFactoryInterface
{
    public function paginateByUser(int $userId, string $profileXid, ?int $skip = null, ?int $limit = null, ?string $sortBy = null, ?string $keyword = null);

    public function findByMonth(\DateTimeImmutable $dateTime);
}
