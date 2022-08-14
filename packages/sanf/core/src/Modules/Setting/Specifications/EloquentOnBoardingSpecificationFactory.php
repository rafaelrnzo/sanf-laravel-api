<?php

namespace Sanf\Core\Modules\Setting\Specifications;

class EloquentOnBoardingSpecificationFactory implements OnBoardingSpecificationFactoryInterface
{
    public function browse(int $limit = null, string $sortBy = null)
    {
        return new EloquentBrowseOnBoardingSpecification($limit, $sortBy);
    }
}
