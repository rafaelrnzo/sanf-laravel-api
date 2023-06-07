<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadServiceCartByUserService implements ApplicationServiceInterface
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
        $productServiceRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productServiceRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        $productServiceResponse = $this->productRepository->get(
            $this->productSpecification->readService($productServiceRecord->snapshot_response_body->xid)
        );

        $data = (array)$productServiceResponse->data;
        unset($data['reviews']);
        $productServiceResponseDto =  new ReadProductServiceResponseDto($data);
        $productServiceResponseDto->xid = $productServiceRecord->xid;
        $productServiceResponseDto->customerReviews = [];

        return $productServiceResponseDto;
    }
}
