<?php

namespace Sanf\Core\Modules\Setting\Specifications;

interface FrequentlyAskQuestionSpecificationFactoryInterface
{
    public function paginate(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null);

    public function paginateCategory(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null);

    public function paginateWebview(int $categoryId = null, bool $isPopular = null, string $keyword = null, int $limit = null, int $skip = null, string $orderBy = null);

    public function paginateCategoryWebview(string $keyword = null, int $limit = null, int $skip = null, string $sortBy = null);
}
