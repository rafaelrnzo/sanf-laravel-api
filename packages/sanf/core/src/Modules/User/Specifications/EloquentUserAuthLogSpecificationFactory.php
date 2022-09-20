<?php

namespace Sanf\Core\Modules\User\Specifications;

class EloquentUserAuthLogSpecificationFactory implements UserAuthLogSpecificationFactoryInterface
{
    public function paginateByUserId(
        int $userId,
        array $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    ) {
        return new EloquentBrowseUserAuthLogByUserIdSpecification($userId, $statusId, $keyword, $limit, $skip, $sortBy);
    }

    public function paginateByExternal(
        int $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    ) {
        return new EloquentBrowseUserAuthLogByExternalSpecification($statusId, $keyword, $limit, $skip, $sortBy);
    }
}
