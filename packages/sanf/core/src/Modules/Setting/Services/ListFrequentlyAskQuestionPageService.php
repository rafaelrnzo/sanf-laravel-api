<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;
use Sanf\Core\Modules\Setting\Specifications\FrequentlyAskQuestionSpecificationFactoryInterface;

class ListFrequentlyAskQuestionPageService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;
    private FrequentlyAskQuestionSpecificationFactoryInterface $specification;

    public function __construct(
        FrequentlyAskQuestionRepositoryInterface $repository,
        FrequentlyAskQuestionSpecificationFactoryInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        return $this->repository->query(
            $this->specification->paginateWebview($dto->categoryId, $dto->isPopular, $dto->keyword, $dto->limit, $dto->skip, $dto->sortBy)
        );
    }
}
