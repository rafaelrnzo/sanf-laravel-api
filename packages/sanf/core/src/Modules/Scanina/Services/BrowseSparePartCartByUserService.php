<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparepartResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class BrowseSparePartCartByUserService implements ApplicationServiceInterface
{

    private ProductCartRepositoryInterface $repository;
    private ProductCartSpecificationInterface $specification;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        ProductCartRepositoryInterface $repository,
        ProductCartSpecificationInterface $specification,
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $paramSize = (object)[
            'profileXid' => $dto->profileXid,
            'productType' => ScaninaProductTypeEnum::SPARE_PART
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

        $dto->productType = ScaninaProductTypeEnum::SPARE_PART;
        $records = $this->repository->query(
            $this->specification->listByUser($dto)
        );

        $responseProductSparePart = $this->syncWithApi($records);

        return (object)[
            'data' => $responseProductSparePart,
            'paginate' => (object)[
                'total' => $size,
                'count' => count($records),
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }

    private function syncWithApi(array $records): array
    {
        $responses = [];
        foreach ($records as $model) {
            $product =  new BrowseProductSparePartResponseDto((array)$model->snapshot_response_body);

            $productSparePartResponse = $this->productRepository->get(
                $this->productSpecification->readSparePart($product->xid)
            );

            $data = (array)$productSparePartResponse->data;
            unset($data['reviews']);
            $productSparePartResponseDto =  new ReadProductSparePartResponseDto($data);
            $productSparePartResponseDto->id = $model->id;
            $productSparePartResponseDto->xid = $model->xid;
            $productSparePartResponseDto->quantity = $model->snapshot_request_body->quantity;
            $productSparePartResponseDto->customerReviews = [];

            $responses[] = $productSparePartResponseDto;
        }

        return $responses;
    }
}
