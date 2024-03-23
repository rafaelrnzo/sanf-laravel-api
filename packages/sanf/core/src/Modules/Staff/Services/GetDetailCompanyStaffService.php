<?php

namespace Sanf\Core\Modules\Staff\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Staff\GeneralStaffException;
use Sanf\Core\Modules\Staff\StaffService;

class GetDetailCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $response = $this->internalApiClient->getStaffs($dto->xid);
        $staff = collect($response['data'])->firstWhere('SR_NO', $dto->no);
        if (is_null($staff)) {
            throw new GeneralStaffException('Staff Not Found');
        }
        if (!filter_var($staff['EMAIL'], FILTER_VALIDATE_EMAIL)) {
            throw new GeneralStaffException('Invalid Email Format');
        }
        $invitedStaff = $this->staffRepository->findByCompanyXidAndUserId($dto->xid, $staff['CUST_ID']);
        if ($invitedStaff) {
            throw new GeneralStaffException('Already Invited');
        }

        return (object) [
            'customerId' => $staff['CUST_ID'],
            'no' => $staff['SR_NO'],
            'title' => $staff['CUST_TITLE'],
            'position' => $staff['JABATAN'],
            'share_percentage' => $staff['PERC_SHARE'],
            'fullName' => $staff['CUST_NAME'],
            'type' => $staff['F_PC'],
            'email' => $staff['EMAIL'],
        ];
    }
}
