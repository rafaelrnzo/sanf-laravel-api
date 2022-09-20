<?php

namespace Sanf\Core\Modules\User\Specifications;

class EloquentUserAuthLogSpecificationFactory implements UserAuthLogSpecificationFactoryInterface
{
    public function paginate(
        int $statusId = null,
        string $keyword = null,
        int $limit = null,
        int $skip = null,
        string $sortBy = null
    ) {
        return new EloquentBrowseUserAuthLogSpecification($statusId, $keyword, $limit, $skip, $sortBy);
    }
}
