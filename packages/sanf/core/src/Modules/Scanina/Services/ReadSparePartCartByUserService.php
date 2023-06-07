<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadSparePartCartByUserService implements ApplicationServiceInterface
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
        $productSparePartRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productSparePartRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        $productSparePartResponse = $this->productRepository->get(
            $this->productSpecification->readSparePart($productSparePartRecord->snapshot_response_body->xid)
        );

        $data = (array)$productSparePartResponse->data;
        $productSparePartResponseDto =  new ReadProductSparePartResponseDto($data);
        $productSparePartResponseDto->xid = $productSparePartRecord->xid;
        $productSparePartResponseDto->customerReviews = [];

        return $productSparePartResponseDto;
    }
}
