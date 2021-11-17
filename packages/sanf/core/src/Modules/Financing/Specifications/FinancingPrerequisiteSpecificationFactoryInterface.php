<?php


namespace Sanf\Core\Modules\Financing\Specifications;


interface FinancingPrerequisiteSpecificationFactoryInterface
{
    public function paginate(?int $skip = null, ?int $limit = null, ?string $sort_by = null);
}
