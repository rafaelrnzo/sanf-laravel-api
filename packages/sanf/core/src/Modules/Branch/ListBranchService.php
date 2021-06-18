<?php


namespace Sanf\Core\Modules\Branch;


use Sanf\Api\Modules\Branch\ListBranchResultDto;
use Sanf\Core\Modules\ServiceInterface;

class ListBranchService implements ServiceInterface
{
    protected BranchRepositoryInterface $repository;

    public function __construct(BranchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function run($dto)
    {
        // sent list data
        return new ListBranchResultDto([
            'list' => $this->repository->list($dto->limit, $dto->offset)
        ]);
    }
}
