<?php

namespace Sanf\Core\Modules\Contract\Specifications;

use Sanf\Core\Modules\Contract\Specifications\EloquentPaginateDocumentAssigneeByUserIdSpecification;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;

class EloquentESignDocumentSpecificationFactory implements ESignDocumentSpecificationFactoryInterface
{
    public function paginateDocumentAssigneeByUserId(
        int $userId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        return new EloquentPaginateDocumentAssigneeByUserIdSpecification($userId, $statusId, $keyword, $sortBy, $skip, $limit, $timestamp);
    }

    public function paginateDocumentAssigneeByDocId(
        string $documentId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        return new EloquentPaginateDocumentAssigneeByDocIdSpecification($documentId, $statusId, $keyword, $sortBy, $skip, $limit, $timestamp);
    }
}
