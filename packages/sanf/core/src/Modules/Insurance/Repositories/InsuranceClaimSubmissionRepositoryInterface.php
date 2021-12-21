<?php

namespace Sanf\Core\Modules\Insurance\Repositories;


interface InsuranceClaimSubmissionRepositoryInterface
{
    public function findById($id);

    public function findByXid($xid);

    public function query($specification);

    public function add($fields);

    public function update($fields);

    public function remove($fields);

    public function size($specification = null);
}
