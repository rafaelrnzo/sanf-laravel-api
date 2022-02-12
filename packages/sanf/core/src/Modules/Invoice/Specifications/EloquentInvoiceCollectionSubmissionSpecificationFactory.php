<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

class EloquentInvoiceCollectionSubmissionSpecificationFactory implements InvoiceCollectionSubmissionSpecificationFactoryInterface
{
    public function paginateByUserAndProfile(int $userId, string $profileXid, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null)
    {
        return new EloquentPaginateInvoiceCollectionSubmissionByUserSpecification($userId, $profileXid, $keyword, $statusId, $sortBy, $skip, $limit, $timestamp);
    }

    public function whereBySerialNoAndUserAndStatus(string $serialNo, int $userId, array $status)
    {
        return new EloquentWhereInvoiceCollectionSubmissionBySerialNoAndUserAndStatusSpecification($serialNo, $userId, $status);
    }
}
