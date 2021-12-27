<?php

namespace Sanf\Core\Modules\Contract\Specifications;

class EloquentFinancingUnitLocationSubmissionSpecificationFactory implements FinancingUnitLocationSubmissionSpecificationFactoryInterface
{
    public function paginate(?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null)
    {
        return new EloquentPaginateFinancingUnitLocationSubmissionSpecification($keyword, $statusId, $sortBy, $skip, $limit, $timestamp);
    }

    public function paginateByUser(?int $userId, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, $timestamp = null)
    {
        return new EloquentPaginateFinancingUnitLocationSubmissionByUserSpecification($userId, $keyword, $statusId, $sortBy, $skip, $limit, $timestamp);
    }

    public function getWhereContractNumberAndIsProcess(?int $userId, string $contractNumber)
    {
        return new EloquentWhereContractNumberAndIsProcess($userId, $contractNumber);
    }
}
