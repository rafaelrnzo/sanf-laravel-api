<?php

namespace Sanf\Core\Modules\Faq\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Faq\Repositories\FaqCategoryRepositoryInterface;

class DetailFaqCategoryService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(FaqCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        return $this->repository->findById($dto->id);
    }
}
