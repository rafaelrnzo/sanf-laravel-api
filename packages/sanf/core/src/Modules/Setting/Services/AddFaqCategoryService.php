<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\FaqCategoryRepositoryInterface;

class AddFaqCategoryService implements ApplicationServiceInterface
{
    private FaqCategoryRepositoryInterface $repository;

    public function __construct(FaqCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        $this->repository->create([
            'name' => $dto->name,
        ]);

        return true;
    }
}
