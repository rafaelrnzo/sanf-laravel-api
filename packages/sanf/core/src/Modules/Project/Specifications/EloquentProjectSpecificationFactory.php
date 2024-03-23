<?php

namespace Sanf\Core\Modules\Project\Specifications;

class EloquentProjectSpecificationFactory implements ProjectSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?int $timestamp, ?string $keyword)
    {
        return new EloquentPaginateProjectSpecification($skip, $limit, $sortBy, $timestamp, $keyword);
    }

    public function paginateByUser($userId, ?int $skip, ?int $limit, ?string $sortBy, ?int $timestamp, ?string $keyword)
    {
        return new EloquentPaginateUserProjectSpecification($userId, $skip, $limit, $sortBy, $timestamp, $keyword);
    }

    public function getAllOwned($userId)
    {
        return new EloquentAllOwnedProjectSpecification($userId);
    }

    public function getAllOwnedByStatus($userId, array $statuses)
    {
        return new EloquentAllOwnedProjectByStatusSpecification($userId, $statuses);
    }
}
