<?php

namespace Sanf\Core\Modules\PdcHold\Specifications;

/**
 * @since CR2025
 */
interface PdcHoldSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?int $statusId = null, ?int $type = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null);
}
