<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;

class BrowseBuyCartByUserService implements ApplicationServiceInterface
{

    private ProductCartRepositoryInterface $repository;
    private ProductCartSpecificationInterface $specification;

    public function __construct(
        ProductCartRepositoryInterface $repository,
        ProductCartSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $paramSize = (object)[
            'profileXid' => $dto->profileXid,
            'productType' => ScaninaProductTypeEnum::BUY
        ];
        $size = $this->repository->size(
            $this->specification->listByUser($paramSize)
        );

        if ($size === 0) {
            return (object)[
                'data' => [],
                'paginate' => (object)[
                    'total' => 0,
                    'count' => 0,
                    'skip' => $dto->skip,
                    'limit' => $dto->limit,
                    'sort_by' => $dto->sortBy,
                ],
            ];
        }

        $dto->productType = ScaninaProductTypeEnum::BUY;
        $records = $this->repository->query(
            $this->specification->listByUser($dto)
        );

        $responseProductBuy = array_map(function ($record) {
            $product =  new BrowseProductBuyResponseDto((array)$record->snapshot_response_body);
            $product->id = $record->id;
            $product->xid = $record->xid;
            return $product;
        }, $records);

        return (object)[
            'data' => $responseProductBuy,
            'paginate' => (object)[
                'total' => $size,
                'count' => count($records),
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
