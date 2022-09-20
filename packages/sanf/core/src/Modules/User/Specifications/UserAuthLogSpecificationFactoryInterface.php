<?php

namespace Sanf\Core\Modules\User\Specifications;

interface UserAuthLogSpecificationFactoryInterface
{
    public function paginateByUserId(
        int $userId,
        array $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    );

    public function paginateByExternal(
        int $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    );
}
