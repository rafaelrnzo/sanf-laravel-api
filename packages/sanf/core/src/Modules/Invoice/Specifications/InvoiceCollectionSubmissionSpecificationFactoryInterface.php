<?php

namespace Sanf\Core\Modules\Invoice\Specifications;

interface InvoiceCollectionSubmissionSpecificationFactoryInterface
{
    public function paginateByUser(int $userId, ?string $keyword = null, ?int $statusId = null, ?string $sortBy = null, ?int $skip = null, ?int $limit = null, ?int $timestamp = null);
}
