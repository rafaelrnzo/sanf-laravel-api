<?php

namespace Sanf\Core\Modules\Setting\Specifications;

class EloquentFrequentlyAskQuestionSpecificationFactory implements FrequentlyAskQuestionSpecificationFactoryInterface
{
    public function paginate(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null)
    {
        return new EloquentBrowseFrequentlyAskQuestionSpecification($keyword, $limit, $skip, $sortBy);
    }

    public function paginateCategory(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null)
    {
        return new EloquentBrowseFrequentlyAskQuestionCategorySpecification($keyword, $limit, $skip, $sortBy);
    }

    public function paginateWebview(int $categoryId = null, bool $isPopular = null, string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null)
    {
        return new EloquentBrowseFrequentlyAskQuestionPageSpecification($categoryId, $isPopular, $keyword, $limit, $skip, $sortBy);
    }

    public function paginateCategoryWebview(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null)
    {
        return new EloquentBrowseFrequentlyAskQuestionCategoryPageSpecification($keyword, $limit, $skip, $sortBy);
    }
}
