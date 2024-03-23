<?php

namespace Sanf\Core\Modules\Project\Specifications;

interface ProjectSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?int $timestamp, ?string $keyword);

    public function paginateByUser($userId, ?int $skip, ?int $limit, ?string $sortBy, ?int $timestamp, ?string $keyword);

    public function getAllOwned($userId);

    public function getAllOwnedByStatus($userId, array $statuses);
}
