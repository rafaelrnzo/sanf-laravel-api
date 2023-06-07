<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductReviewDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadRentCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        ProductCartRepositoryInterface $repository,
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $productRentRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productRentRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        $productRentResponse = $this->productRepository->get(
            $this->productSpecification->readRent($productRentRecord->snapshot_response_body->xid)
        );

        $data = (array)$productRentResponse->data;
        unset($data['review']);

        $productRentResponseDto =  new ReadProductRentResponseDto($data);
        $productRentResponseDto->xid = $productRentRecord->xid;

        $productRentSpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification(
                $productRentRecord->snapshot_response_body->xid,
                ScaninaProductTypeEnum::RENT
            )
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

        return $productRentResponseDto;
    }
}
