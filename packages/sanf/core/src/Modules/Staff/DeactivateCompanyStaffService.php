<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;

class DeactivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
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

        $user = $this->userRepository->findByEmail($staff['EMAIL']);
        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        $invitedStaff = $this->staffRepository->findByCompanyXidAndUserId($dto->xid, $user->id);
        if (!$invitedStaff) {
            throw new GeneralStaffException('User Not Found');
        }

        return $this->staffRepository->removeById($invitedStaff->id);
    }
}
