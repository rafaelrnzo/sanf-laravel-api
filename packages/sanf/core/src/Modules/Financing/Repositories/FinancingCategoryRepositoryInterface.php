<?php

namespace Sanf\Core\Modules\Financing\Repositories;

interface FinancingCategoryRepositoryInterface
{
    public function query($specification): array;

    public function size($specification): int;
}
