<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class BrowseBuyCartByUserService implements ApplicationServiceInterface
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

        $responseProductBuy = $this->syncWithApi($records);

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

    private function syncWithApi(array $records): array
    {
        $responses = [];
        foreach ($records as $model) {
            $product =  new BrowseProductBuyResponseDto((array)$model->snapshot_response_body);

            $productBuyResponse = $this->productRepository->get(
                $this->productSpecification->readBuy($product->xid)
            );

            $data = (array)$productBuyResponse->data;
            unset($data['review']);
            $productBuyResponseDto = new ReadProductBuyResponseDto($data);
            $productBuyResponseDto->id = $model->id;
            $productBuyResponseDto->xid = $model->xid;

            $productBuySpecificationResponse = $this->productRepository->get(
                $this->productSpecification->getSpecification($product->xid, ScaninaProductTypeEnum::BUY)
            );

            $specifications = array_map(function ($specification) {
                $specification->subSpecification = array_map(function ($subSpecification) {
                    return new BrowseProductSubSpecificationResponseDto((array)$subSpecification);
                }, $specification->subSpecification);

                return new BrowseProductSpecificationResponseDto((array)$specification);
            }, $productBuySpecificationResponse->data->rows);

            $subSpecifications = [];
            foreach ($specifications as $specification) {
                foreach ($specification->subSpecification as $subSpecification) {
                    $subSpecifications[] = $subSpecification;
                }
            }
            $productBuyResponseDto->subSpecifications = $subSpecifications;

            $responses[] = $productBuyResponseDto;
        }

        return $responses;
    }
}
