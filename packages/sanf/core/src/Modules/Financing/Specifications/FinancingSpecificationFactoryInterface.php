<?php


namespace Sanf\Core\Modules\Financing\Specifications;


interface FinancingSpecificationFactoryInterface
{
    public function paginate(?int $skip, ?int $limit, ?string $sort_by);

    public function paginateFinancingPrerequisite(?int $skip, ?int $limit, ?string $sort_by);

    public function findById(?int $id);
}
