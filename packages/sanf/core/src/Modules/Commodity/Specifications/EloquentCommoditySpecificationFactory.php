<?php


namespace Sanf\Core\Modules\Commodity\Specifications;


class EloquentCommoditySpecificationFactory implements CommoditySpecificationFactoryInterface
{

    public function paginate(?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateCommoditySpecification($skip, $limit, $sortBy, $keyword);
    }

    public function paginateByUser($userId, ?int $skip, ?int $limit, ?string $sortBy, ?string $keyword)
    {
        return new EloquentPaginateUserCommoditySpecification($userId, $skip, $limit, $sortBy, $keyword);
    }

    public function getAllOwned($userId)
    {
        return new EloquentAllOwnedCommoditySpecification($userId);
    }

    public function getAllOwnedByStatus($userId, array $statuses)
    {
        return new EloquentAllOwnedCommodityByStatusSpecification($userId, $statuses);
    }


}
