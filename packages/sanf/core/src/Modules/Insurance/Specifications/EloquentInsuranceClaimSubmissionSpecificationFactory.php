<?php

namespace Sanf\Core\Modules\Insurance\Specifications;

class EloquentInsuranceClaimSubmissionSpecificationFactory implements InsuranceClaimSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, $timestamp = null)
    {
        return new EloquentPaginateInsuranceClaimSubmissionByUserAndProfileSpecification($userId, $profileXid, $keyword, $statusId, $sortBy, $skip, $limit, $timestamp);
    }
}
