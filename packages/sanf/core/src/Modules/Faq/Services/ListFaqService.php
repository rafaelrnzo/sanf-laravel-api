<?php

namespace Sanf\Core\Modules\Faq\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Faq\Repositories\FaqRepositoryInterface;

class ListFaqService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(FaqRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $filter = [];
        if ($dto->searchKeyword)
            $filter[] = "(title ilike '%{$dto->searchKeyword}%' or description ilike '%{$dto->searchKeyword}%')";

        if ($dto->categoryId)
            $filter[] = "category_id = '{$dto->categoryId}'";

        if (!is_null($dto->isPopular))
            $filter[] = "is_popular = '{$dto->isPopular}'";

        $search  = implode(' and ', $filter);

        return $this->repository->list(
            $dto->limit,
            $dto->offset,
            $dto->orderBy,
            $dto->orderDirection,
            $search
        );
    }
}
