<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
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
        $productSparePartResponseDto =  new ReadProductSparePartResponseDto($data);
        $productSparePartResponseDto->customerReviews = [];

        return $productSparePartResponseDto;
    }
}
