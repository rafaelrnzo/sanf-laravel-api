<?php

namespace Sanf\Core\Modules\User\Specifications;

class EloquentUserAuthLogEncryptedSpecificationFactory implements UserAuthLogSpecificationFactoryInterface
{
    public function paginateByUserId(
        int $userId,
        array $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    ) {
        return new EloquentBrowseUserAuthLogByUserIdEncryptedSpecification($userId, $statusId, $keyword, $limit, $skip, $sortBy);
    }

    public function paginateByExternal(
        int $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    ) {
        return new EloquentBrowseUserAuthLogByExternalEncryptedSpecification($statusId, $keyword, $limit, $skip, $sortBy);
    }
}
