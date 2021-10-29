<?php


namespace Sanf\Core\Modules\Financing\Repositories\FinancingMethod;


interface FinancingRepositoryInterface
{
    public function find($limit, $offset, $sort_by);

    public function query($specification);

    public function get($specification);

    public function size($specifiaction = null);
}
