<?php


namespace Sanf\Core\Modules\Financing\Specifications;


interface FinancingMethodSpecificationFactoryInterface
{
    public function paginate(?int $skip = null, ?int $limit = null, ?string $sort_by = null);

    public function paginateByFacility(int $facilityId, ?int $skip = null, ?int $limit = null, ?string $sort_by = null);

}
