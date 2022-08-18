<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dtos\UpdateFrequentlyAskQuestionCategoryDto;
use Sanf\Core\Modules\Setting\Exceptions\FrequentlyAskQuestionCategoryNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;

class DeleteFrequentlyAskQuestionCategoryService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;

    public function __construct(FrequentlyAskQuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var UpdateFrequentlyAskQuestionCategoryDto $dto */
        $existingFaqCategory = $this->repository->findCategoryById($dto->xid);
        if (!$existingFaqCategory) {
            throw new FrequentlyAskQuestionCategoryNotFoundException();
        }

        $this->repository->destroyCategory($existingFaqCategory->id);

        return true;
    }
}
