<?php

namespace Sanf\Core\Modules\Contract\Repositories;

class EloquentESignDocumentSpecificationFactory implements ESignDocumentSpecificationFactoryInterface
{
    public function paginateByUserId(
        int $userId,
        ?int $statusId,
        ?string $keyword = null,
        ?string $sortBy = null,
        ?int $skip = null,
        ?int $limit = null,
        ?int $timestamp = null
    ) {
        return new EloquentPaginateByUserIdSpecification($userId, $statusId, $keyword, $sortBy, $skip, $limit, $timestamp);
    }
}
