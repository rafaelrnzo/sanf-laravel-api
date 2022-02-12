<?php

namespace Sanf\Core\Modules\Insurance\Specifications;

interface InsuranceClaimSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null);

    public function whereBySerialNoAndUserAndStatus(string $serialNo, int $userId, array $status);
}
