<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;

class ReadBuyCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;

    public function __construct(
        ProductCartRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $productBuyRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productBuyRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        return new ReadProductBuyResponseDto((array)$productBuyRecord->snapshot_response_body);
    }
}
