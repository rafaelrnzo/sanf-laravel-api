<?php

namespace Sanf\Core\Modules\Setting\Specifications;

interface OnBoardingSpecificationFactoryInterface
{
    public function browse(int $limit = null, string $sortBy = null);
}
