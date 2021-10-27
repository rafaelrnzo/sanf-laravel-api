<?php


namespace Sanf\Core\Modules\Financing\Services;

use Sanf\Core\Modules\Financing\Repositories\FinancingRepositoryInterface;
use Spatie\DataTransferObject\DataTransferObject;

class FinancingService extends DataTransferObject
{
    protected $repository;

    public function __construct(FinancingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
