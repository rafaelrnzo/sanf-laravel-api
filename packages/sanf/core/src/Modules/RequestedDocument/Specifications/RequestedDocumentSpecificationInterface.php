<?php

namespace Sanf\Core\Modules\RequestedDocument\Specifications;

interface RequestedDocumentSpecificationInterface
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
    );
}
