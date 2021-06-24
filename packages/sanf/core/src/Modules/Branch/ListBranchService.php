<?php


namespace Sanf\Core\Modules\Branch;


use NbsPhp\Core\Services\ApplicationServiceInterface;

class ListBranchService implements ApplicationServiceInterface
{
    protected BranchRepositoryInterface $repository;

    public function __construct(BranchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto)
    {
        // sent list data
        return $this->repository->list($dto->limit, $dto->offset);
    }
}
