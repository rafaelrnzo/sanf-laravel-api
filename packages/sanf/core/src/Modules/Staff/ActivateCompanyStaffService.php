<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Services\ApplicationServiceInterface;

class ActivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $invitedStaff = $this->staffRepository->findByCompanyXidAndUserId($dto->companyXid, $dto->userId);
        if (!$invitedStaff) {
            $this->staffRepository->add([
                'user_id' => $dto->userId,
                'company_xid' => $dto->companyXid,
                'created_by' => $dto->createdBy,
            ]);
        }
    }
}
