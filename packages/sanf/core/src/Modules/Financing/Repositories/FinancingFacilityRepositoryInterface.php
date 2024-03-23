<?php

namespace Sanf\Core\Modules\Financing\Repositories;

interface FinancingFacilityRepositoryInterface
{
    public function query($specification);

    public function first($specification);

    public function findById($id);

    public function size($specification = null);
}
