<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;

class ReadSparePartCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;

    public function __construct(
        ProductCartRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $productSparePartRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productSparePartRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        return new ReadProductSparePartResponseDto((array)$productSparePartRecord->snapshot_response_body);
    }
}
