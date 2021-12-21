<?php

namespace Sanf\Core\Modules\Contract\Specifications;

interface FinancingUnitLocationSubmissionSpecificationFactoryInterface
{
    public function paginate(?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null);

    public function paginateByUser(?int $userId, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null);
}
