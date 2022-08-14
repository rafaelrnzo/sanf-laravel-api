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
}
