<?php


namespace Sanf\Core\Modules\Financing\Repositories;


interface FinancingFacilityRepositoryInterface
{
    public function query($specification);

    public function findById($id);

    public function size($specifiaction = null);
}
