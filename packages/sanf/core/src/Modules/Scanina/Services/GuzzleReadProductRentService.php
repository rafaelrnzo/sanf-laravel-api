<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductReviewDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleReadProductRentService implements ApplicationServiceInterface
{
    private ScaninaProductRepositoryInterface $repository;
    private ScaninaProductSpecificationInterface $specification;

    public function __construct(
        ScaninaProductRepositoryInterface $repository,
        ScaninaProductSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $productRentResponse = $this->repository->get(
            $this->specification->readRent($dto->xid)
        );

        $data = (array)$productRentResponse->data;
        unset($data['reviews']);
        $productRentResponseDto =  new ReadProductRentResponseDto((array)$productRentResponse->data);

        $reviews = (array)optional($productRentResponse->data)->reviews;
        $productRentResponseDto->reviews = new ReadProductReviewDto($reviews);

        $productRentSpecificationResponse = $this->repository->get(
            $this->specification->getSpecification($dto->xid, ScaninaProductTypeEnum::RENT)
        );

        $productRentResponseDto->specifications = array_map(function ($specification) {
            return new BrowseProductSpecificationResponseDto((array)$specification);
        }, $productRentSpecificationResponse->data->rows);

        return $productRentResponseDto;
    }
}
