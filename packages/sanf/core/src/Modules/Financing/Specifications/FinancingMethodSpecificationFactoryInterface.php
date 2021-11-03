<?php


namespace Sanf\Core\Modules\Financing\Specifications;


interface FinancingMethodSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sort_by);
}
