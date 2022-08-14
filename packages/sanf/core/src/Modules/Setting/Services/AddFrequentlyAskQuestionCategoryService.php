<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;

class AddFrequentlyAskQuestionCategoryService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;

    public function __construct(FrequentlyAskQuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        $this->repository->createCategory([
            'name' => $dto->name,
        ]);

        return true;
    }
}
