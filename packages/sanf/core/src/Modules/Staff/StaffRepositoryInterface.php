<?php

namespace Sanf\Core\Modules\Staff;

interface StaffRepositoryInterface
{
    public function getByCompanyXid($companyXid);

    public function getByUserId($userId);

    public function findByCompanyXidAndUserId($companyXid, $userId);

    public function add($fields);

    public function removeById($id);
}
