<?php

namespace Sanf\Core\Modules\Financing\Repositories;

interface FinancingPrerequisiteRepositoryInterface
{
    public function query($specification);

    public function get($specification);

    public function size($specifiaction = null);
}
