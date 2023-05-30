<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;

class BrowseRentCartByUserService implements ApplicationServiceInterface
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
            'productType' => ScaninaProductTypeEnum::RENT
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

        $dto->productType = ScaninaProductTypeEnum::RENT;
        $records = $this->repository->query(
            $this->specification->listByUser($dto)
        );

        $responseProductRent = array_map(function ($record) {
            $product =  new BrowseProductRentResponseDto((array)$record->snapshot_response_body);
            $product->id = $record->id;
            $product->xid = $record->xid;
            $product->startDate = $record->snapshot_request_body->rentStartDate;
            $product->endDate = $record->snapshot_request_body->rentEndDate;

            return $product;
        }, $records);

        return (object)[
            'data' => $responseProductRent,
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
