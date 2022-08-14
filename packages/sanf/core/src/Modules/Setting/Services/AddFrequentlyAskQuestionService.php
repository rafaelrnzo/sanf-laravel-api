<?php

namespace Sanf\Core\Modules\Setting\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Setting\Dto\AddFrequentlyAskQuestionDto;
use Sanf\Core\Modules\Setting\Exceptions\FrequentlyAskQuestionCategoryNotFoundException;
use Sanf\Core\Modules\Setting\Repositories\FrequentlyAskQuestionRepositoryInterface;

class AddFrequentlyAskQuestionService implements ApplicationServiceInterface
{
    private FrequentlyAskQuestionRepositoryInterface $repository;

    public function __construct(FrequentlyAskQuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($dto = null): bool
    {
        /** @var AddFrequentlyAskQuestionDto $dto */

        $category = $this->repository->findCategoryById($dto->categoryId);
        if (!$category) {
            throw new FrequentlyAskQuestionCategoryNotFoundException();
        }

        $this->repository->create([
            'category_id' => $category->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'is_popular' => $dto->isPopular,
            'order' => (double) $dto->order,
        ]);

        return true;
    }
}
