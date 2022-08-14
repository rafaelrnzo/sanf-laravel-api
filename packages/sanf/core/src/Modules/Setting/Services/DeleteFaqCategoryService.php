<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dto\UpdateFaqCategoryDto;
use Sanf\Core\Modules\Setting\Exceptions\FaqCategoryNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\FaqCategoryRepositoryInterface;

class DeleteFaqCategoryService implements ApplicationServiceInterface
{
    private FaqCategoryRepositoryInterface $repository;

    public function __construct(FaqCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var UpdateFaqCategoryDto $dto */
        $existingFaqCategory = $this->repository->findById($dto->xid);
        if (!$existingFaqCategory) {
            throw new FaqCategoryNotFoundException();
        }

        $this->repository->destroy($existingFaqCategory->id);

        return true;
    }
}
