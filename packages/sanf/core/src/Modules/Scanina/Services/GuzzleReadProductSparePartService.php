<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductCustomerReviewDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleReadProductSparePartService implements ApplicationServiceInterface
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
        $productSparePartResponse = $this->repository->get(
            $this->specification->readSparePart($dto->xid)
        );

        $data = (array)$productSparePartResponse->data;
        unset($data['reviews']);
        $productSparePartResponseDto =  new ReadProductSparePartResponseDto((array)$productSparePartResponse->data);

        $productCustomerReviewResponse = $this->repository->get(
            $this->specification->getCustomerReview($dto->xid, ScaninaProductTypeEnum::SPARE_PART)
        );

        $customerReviews = array_map(function ($review) {
            return new BrowseProductCustomerReviewDto((array)$review->userData);
        }, $productCustomerReviewResponse->data->rows);
        $productSparePartResponseDto->customerReviews = $customerReviews;

        return $productSparePartResponseDto;
    }
}
