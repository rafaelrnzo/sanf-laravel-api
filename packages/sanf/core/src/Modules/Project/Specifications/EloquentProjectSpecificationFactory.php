<?php


namespace Sanf\Core\Modules\Project\Specifications;


class EloquentProjectSpecificationFactory implements ProjectSpecificationFactoryInterface
{

    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateProjectSpecification($skip, $limit, $sortBy, $keyword);
    }

    public function paginateByUser($userId, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateUserProjectSpecification($userId, $skip, $limit, $sortBy, $keyword);
    }
}
