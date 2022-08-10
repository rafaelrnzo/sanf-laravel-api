<?php

namespace Sanf\Core\Modules\OnBoarding\Specifications;

interface OnBoardingSpecificationFactoryInterface
{
    public function browse(int $limit = null, string $sortBy = null);
}
