<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadServiceCartByUserService implements ApplicationServiceInterface
{
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $productServiceResponse = $this->productRepository->get(
            $this->productSpecification->readService($dto->productXid)
        );

        $data = (array) $productServiceResponse->data;
        unset($data['reviews']);
        $productServiceResponseDto = new ReadProductServiceResponseDto($data);
        $productServiceResponseDto->xid = $dto->productXid;
        $productServiceResponseDto->customerReviews = [];

        return $productServiceResponseDto;
    }
}
