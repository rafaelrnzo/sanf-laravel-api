<?php

namespace Sanf\Core\Modules\User\Specifications;

class EloquentUserAuthSpecificationFactory implements UserAuthSpecificationFactoryInterface
{
    public function paginateUserActive(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy)
    {
        return new EloquentPaginateUserActiveSpecification($keyword, $skip, $limit, $sortBy);
    }
}
