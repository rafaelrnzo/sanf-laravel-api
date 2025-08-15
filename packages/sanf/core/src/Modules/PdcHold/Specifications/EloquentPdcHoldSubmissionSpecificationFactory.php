<?php

namespace Sanf\Core\Modules\PdcHold\Specifications;

/**
 * @since CR2025
 */
class EloquentPdcHoldSubmissionSpecificationFactory implements PdcHoldSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?int $statusId = null, ?int $type = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null)
    {
        return new EloquentPaginatePdcHoldSubmissionByUserAndProfileSpecification($userId, $profileXid, $statusId, $type, $sortBy, $skip, $limit);
    }
}
