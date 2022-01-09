<?php

namespace Sanf\Core\Modules\Financing\Repositories;


interface FinancingApplicationRepositoryInterface
{
    public function findById($id);

    public function findByXid($userId, $xid, $applicationXid);

    public function query($specification);

    public function add($fields);

    public function update($fields, $specification = null);

    public function remove($specification);

    public function removeById($id);

    public function removeByXid($xid);

    public function size($specification = null);
}
