<?php

namespace Sanf\Core\Modules\Setting\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;
use Sanf\Core\Modules\Setting\Specifications\FrequentlyAskQuestionSpecificationFactoryInterface;

class BrowseFrequentlyAskQuestionCategoryService implements ApplicationServiceInterface
{
    protected FrequentlyAskQuestionRepositoryInterface $repository;
    protected FrequentlyAskQuestionSpecificationFactoryInterface $specification;

    public function __construct(
        FrequentlyAskQuestionRepositoryInterface $repository,
        FrequentlyAskQuestionSpecificationFactoryInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $query = $this->repository->queryCategory(
            $this->specification->paginateCategory($dto->keyword, $dto->limit, $dto->skip, $dto->sortBy)
        );

        $total = $this->repository->sizeCategory(
            $this->specification->paginateCategory($dto->keyword)
        );

        $mappingData =  array_map(function ($item) {
            return (object) [
                'id' => $item->id,
                'name' => $item->name ?? null,
                'createdAt' => $item->created_at ?? Carbon::now(),
                'updatedAt' => $item->updated_at ?? Carbon::now(),
            ];
        }, $query);

        return (object)[
            'data' => $mappingData,
            'paginate' => (object)[
                'total' => $total,
                'count' => count($mappingData),
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}