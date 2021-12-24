<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

interface InvoiceCollectionSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null);

    public function whereStillProcessedBySerialNoAndUser(string $serialNo, int $userId);
}
