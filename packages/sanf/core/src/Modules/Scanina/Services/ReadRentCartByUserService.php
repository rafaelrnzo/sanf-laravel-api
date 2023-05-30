<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;

class ReadRentCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;

    public function __construct(
        ProductCartRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $productRentRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productRentRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        return new ReadProductRentResponseDto((array)$productRentRecord->snapshot_response_body);
    }
}
