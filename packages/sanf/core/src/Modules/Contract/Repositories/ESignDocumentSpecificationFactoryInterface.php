<?php

namespace Sanf\Core\Modules\Contract\Repositories;

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
}