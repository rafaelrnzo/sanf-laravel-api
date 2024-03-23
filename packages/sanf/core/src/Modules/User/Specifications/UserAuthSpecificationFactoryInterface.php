<?php

namespace Sanf\Core\Modules\User\Specifications;

interface UserAuthSpecificationFactoryInterface
{
    public function paginateUserActive(?string $keyword, ?int $skip, ?int $limit, ?string $sortBy);
}
