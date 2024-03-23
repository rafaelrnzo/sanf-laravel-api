<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadSparePartCartByUserService implements ApplicationServiceInterface
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
        $productSparePartResponse = $this->productRepository->get(
            $this->productSpecification->readSparePart($dto->productXid)
        );

        $data = (array) $productSparePartResponse->data;
        $productSparePartResponseDto = new ReadProductSparePartResponseDto($data);
        $productSparePartResponseDto->xid = $dto->productXid;
        $productSparePartResponseDto->customerReviews = [];

        return $productSparePartResponseDto;
    }
}
