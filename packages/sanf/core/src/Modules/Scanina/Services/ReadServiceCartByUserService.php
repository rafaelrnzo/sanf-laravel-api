<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;

class ReadServiceCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;

    public function __construct(
        ProductCartRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $productServiceRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productServiceRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        return new ReadProductServiceResponseDto((array)$productServiceRecord->snapshot_response_body);
    }
}
