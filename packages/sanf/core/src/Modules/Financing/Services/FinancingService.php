<?php


namespace Sanf\Core\Modules\Financing\Services;

class FinancingService
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }
}
