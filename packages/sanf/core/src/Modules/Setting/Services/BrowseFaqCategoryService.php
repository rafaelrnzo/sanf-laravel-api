<?php

namespace Sanf\Core\Modules\Setting\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\FaqCategoryRepositoryInterface;
use Sanf\Core\Modules\Setting\Specifications\FaqCategorySpecificationFactoryInterface;

class BrowseFaqCategoryService implements ApplicationServiceInterface
{
    protected FaqCategoryRepositoryInterface $repository;
    protected FaqCategorySpecificationFactoryInterface $specification;

    public function __construct(
        FaqCategoryRepositoryInterface $repository,
        FaqCategorySpecificationFactoryInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $query = $this->repository->query(
            $this->specification->browse($dto->limit, $dto->skip, $dto->sortBy)
        );

        return array_map(function ($item) {
            return (object) [
                'id' => $item->id,
                'name' => $item->name ?? null,
                'createdAt' => $item->created_at ?? Carbon::now(),
            ];
        }, $query);
    }
}