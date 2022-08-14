<?php

namespace Sanf\Core\Modules\Setting\Specifications;

interface FaqCategorySpecificationFactoryInterface
{
    public function browse(int $limit = null, int $skip = null, string $sortBy = null);
}
