<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dtos\UpdateFrequentlyAskQuestionCategoryDto;
use Sanf\Core\Modules\Setting\Exceptions\FrequentlyAskQuestionNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;

class DeleteFrequentlyAskQuestionService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;

    public function __construct(FrequentlyAskQuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var UpdateFrequentlyAskQuestionCategoryDto $dto */
        $existingFaq = $this->repository->findById($dto->xid);
        if (!$existingFaq) {
            throw new FrequentlyAskQuestionNotFoundException();
        }

        $this->repository->destroy($existingFaq->id);

        return true;
    }
}
