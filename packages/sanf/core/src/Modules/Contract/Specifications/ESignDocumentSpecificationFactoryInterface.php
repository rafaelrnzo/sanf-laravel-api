<?php

namespace Sanf\Core\Modules\Contract\Specifications;

interface ESignDocumentSpecificationFactoryInterface
{
    public function paginateDocumentAssigneeByUserId(
        int $userId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    );

    public function paginateDocumentAssigneeByDocId(
        string $documentId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    );
}
