<?php

namespace Sanf\Core\Modules\Setting\Specifications;

class EloquentFaqCategorySpecificationFactory implements FaqCategorySpecificationFactoryInterface
{
    public function browse(int $limit = null, int $skip = null, string $sortBy = null)
    {
        return new EloquentBrowseFaqCategorySpecification($limit, $skip, $sortBy);
    }
}
