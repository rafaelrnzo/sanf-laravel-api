<?php

namespace Sanf\Core\Modules\User\Specifications;

class EloquentUserAuthEncryptedSpecificationFactory implements UserAuthSpecificationFactoryInterface
{
    public function paginateUserActive(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy)
    {
        return new EloquentPaginateUserActiveEncryptedSpecification($keyword, $skip, $limit, $sortBy);
    }
}
