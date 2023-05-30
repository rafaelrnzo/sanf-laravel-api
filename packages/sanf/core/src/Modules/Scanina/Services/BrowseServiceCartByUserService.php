<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;

class BrowseServiceCartByUserService implements ApplicationServiceInterface
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
            'productType' => ScaninaProductTypeEnum::SERVICE
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

        $dto->productType = ScaninaProductTypeEnum::SERVICE;
        $records = $this->repository->query(
            $this->specification->listByUser($dto)
        );

        $responseProductRent = array_map(function ($record) {
            $product =  new BrowseProductRentResponseDto((array)$record->snapshot_response_body);
            $product->id = $record->id;
            $product->xid = $record->xid;
            $product->servicedAt = $record->snapshot_request_body->serviceDate;
            $product->notes = optional($record->snapshot_request_body)->notes;

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
