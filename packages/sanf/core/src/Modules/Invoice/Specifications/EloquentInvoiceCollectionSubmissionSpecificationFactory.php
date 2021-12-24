<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

class EloquentInvoiceCollectionSubmissionSpecificationFactory implements InvoiceCollectionSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null)
    {
        return new EloquentPaginateInvoiceCollectionSubmissionByUserSpecification($userId, $profileXid, $keyword, $statusId, $sortBy, $skip, $limit, $timestamp);
    }

    public function whereStillProcessedBySerialNoAndUser(string $serialNo, int $userId)
    {
        return new EloquentWhereInvoiceCollectionSubmissionStillProcessedByContractAndUserSpecification($serialNo, $userId);
    }
}
