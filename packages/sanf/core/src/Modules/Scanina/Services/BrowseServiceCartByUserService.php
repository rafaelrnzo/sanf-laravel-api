<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class BrowseServiceCartByUserService implements ApplicationServiceInterface
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

        $responseProductService = $this->syncWithApi($records);

        return (object)[
            'data' => $responseProductService,
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
            $product =  new BrowseProductServiceResponseDto((array)$model->snapshot_response_body);

            $productServiceResponse = $this->productRepository->get(
                $this->productSpecification->readService($product->xid)
            );

            $data = (array)$productServiceResponse->data;
            unset($data['reviews']);
            $productServiceResponseDto =  new ReadProductServiceResponseDto($data);
            $productServiceResponseDto->id = $model->id;
            $productServiceResponseDto->xid = $model->xid;
            $productServiceResponseDto->servicedAt = $model->snapshot_request_body->serviceDate;
            $productServiceResponseDto->notes = optional($model->snapshot_request_body)->notes;
            $productServiceResponseDto->customerReviews = [];

            $responses[] = $productServiceResponseDto;
        }

        return $responses;
    }
}
