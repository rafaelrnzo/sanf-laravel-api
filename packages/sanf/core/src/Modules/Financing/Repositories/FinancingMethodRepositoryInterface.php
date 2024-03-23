<?php

namespace Sanf\Core\Modules\Financing\Repositories;

interface FinancingMethodRepositoryInterface
{
    public function find($limit, $offset, $sort_by);

    public function findById($id);

    public function query($specification);

    public function get($specification);

    public function size($specifiaction = null);
}
