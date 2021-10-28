<?php


namespace Sanf\Core\Modules\Financing\Services;

use Sanf\Core\Modules\Financing\Repositories\FinancingRepositoryInterface;

class FinancingService
{
    protected $repository;

    public function __construct(FinancingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
