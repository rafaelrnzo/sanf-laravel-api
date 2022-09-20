<?php

namespace Sanf\Core\Modules\User\Specifications;

interface UserAuthLogSpecificationFactoryInterface
{
    public function paginate(
        int $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    );
}
