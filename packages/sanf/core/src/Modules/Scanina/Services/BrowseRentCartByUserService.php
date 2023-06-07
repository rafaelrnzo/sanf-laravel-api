<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Exception;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ProductCartSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class BrowseRentCartByUserService implements ApplicationServiceInterface
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

        $responseProductRent = $this->syncWithApi($records);

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

    private function syncWithApi(array $records): array
    {
        $responses = [];
        foreach ($records as $model) {
            $product =  new BrowseProductRentResponseDto((array)$model->snapshot_response_body);

            try {
                $productRentResponse = $this->productRepository->get(
                    $this->productSpecification->readRent($product->xid)
                );
            } catch (Exception $exception) {
                if ($exception instanceof ScaninaProductNotFoundException) {
                    continue;
                }
                throw $exception;
            }

            $data = (array)$productRentResponse->data;
            unset($data['review']);
            $productRentResponseDto = new ReadProductRentResponseDto($data);

            $productRentSpecificationResponse = $this->productRepository->get(
                $this->productSpecification->getSpecification($product->xid, ScaninaProductTypeEnum::RENT)
            );

            $specifications = array_map(function ($specification) {
                $specification->subSpecification = array_map(function ($subSpecification) {
                    return new BrowseProductSubSpecificationResponseDto((array)$subSpecification);
                }, $specification->subSpecification);

                return new BrowseProductSpecificationResponseDto((array)$specification);
            }, $productRentSpecificationResponse->data->rows);

            $subSpecifications = [];
            foreach ($specifications as $specification) {
                foreach ($specification->subSpecification as $subSpecification) {
                    $subSpecifications[] = $subSpecification;
                }
            }
            $productRentResponseDto->subSpecifications = $subSpecifications;

            $productRentResponseDto->id = $model->id;
            $productRentResponseDto->xid = $model->xid;
            $productRentResponseDto->startDate = $model->snapshot_request_body->rentStartDate;
            $productRentResponseDto->endDate = $model->snapshot_request_body->rentEndDate;

            $responses[] = $productRentResponseDto;
        }

        return $responses;
    }
}
