<?php

namespace Sanf\Core\Modules\Contract\Repositories;

interface FinancingUnitLocationSubmissionRepositoryInterface
{
    public function findById($id);

    public function findByXid($xid);

    public function findByContractNo($userId, $contractNo);

    public function findByContractNoAndSerialNo($userId, $contractNo, $serialNo);

    public function query($specification);

    public function add($fields);

    public function update($fields);

    public function remove($fields);

    public function size($specification = null);
}
