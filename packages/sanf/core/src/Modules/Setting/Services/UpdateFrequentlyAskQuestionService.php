<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dtos\UpdateFrequentlyAskQuestionDto;
use Sanf\Core\Modules\Setting\Exceptions\FrequentlyAskQuestionCategoryNotFoundException;
use Sanf\Core\Modules\Setting\Exceptions\FrequentlyAskQuestionNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;

class UpdateFrequentlyAskQuestionService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;

    public function __construct(FrequentlyAskQuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var UpdateFrequentlyAskQuestionDto $dto */
        $existingFaq = $this->repository->findById($dto->xid);
        if (!$existingFaq) {
            throw new FrequentlyAskQuestionNotFoundException();
        }

        $category = $this->repository->findById($dto->categoryId);
        if (!$category) {
            throw new FrequentlyAskQuestionCategoryNotFoundException();
        }

        $this->repository->update($existingFaq->id, [
            'category_id' => $category->id ?? $existingFaq->categoryId,
            'title' => $dto->title ?? $existingFaq->title,
            'description' => $dto->description ?? $existingFaq->description,
            'is_popular' => $dto->isPopular ?? $existingFaq->isPopular,
            'order' => (float) $dto->order ?? $existingFaq->order,
        ]);

        return true;
    }
}
