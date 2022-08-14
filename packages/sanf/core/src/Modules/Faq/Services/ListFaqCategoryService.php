<?php

namespace Sanf\Core\Modules\Faq\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Faq\Repositories\FaqCategoryRepositoryInterface;

class ListFaqCategoryService implements ApplicationServiceInterface
{
    protected $repository;

    public function __construct(FaqCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null)
    {
        $filter = [];
        if ($dto->id)
            $filter[] = "id = '{$dto->id}'";

        $filterFaq = [];
        if ($dto->searchFaqKeyword) {
            $filterFaq[] = "(title ilike '%{$dto->searchFaqKeyword}%' or description ilike '%{$dto->searchFaqKeyword}%')";
        }

        $search = implode(' and ', $filter);
        $searchFaq = implode(' and ', $filterFaq);

        return $this->repository->list(
            $dto->limit,
            $dto->offset,
            $dto->orderBy,
            $dto->orderDirection,
            $search,
            $searchFaq
        );
    }
}
