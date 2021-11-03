<?php


namespace Sanf\Core\Modules\Financing\Specifications;


interface FinancingMethodByFacilitySpecificationFactoryInterface
{
    public function paginate(?int $id,?int $skip, ?int $limit, ?string $sort_by);
}
