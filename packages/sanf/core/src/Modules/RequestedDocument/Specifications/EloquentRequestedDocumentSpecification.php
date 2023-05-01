<?php

namespace Sanf\Core\Modules\RequestedDocument\Specifications;

class EloquentRequestedDocumentSpecification implements RequestedDocumentSpecificationInterface
{

    public function paginate(
        string $userId,
        int $statusId,
        ?int $typeId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        return new EloquentPaginateByUserAndStatusSpecification(
            $userId,
            $statusId,
            $typeId,
            $keyword,
            $sortBy,
            $skip,
            $limit,
            $timestamp
        );
    }
}
