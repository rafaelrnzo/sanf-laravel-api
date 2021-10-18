<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Staff\Services\GetDetailCompanyStaffService;
use Sanf\Core\Modules\User\Services\InviteOrGetUserService;

class InviteCompanyStaffAsUserService implements ApplicationServiceInterface
{
    protected GetDetailCompanyStaffService $getDetailCompanyStaffService;
    protected ActivateCompanyStaffService $activateCompanyStaffService;
    protected InviteOrGetUserService $inviteUserService;

    /**
     * InviteCompanyStaffAsUserService constructor.
     * @param ActivateCompanyStaffService $activateCompanyStaffService
     * @param InviteOrGetUserService $inviteUserService
     */
    public function __construct(
        GetDetailCompanyStaffService $getDetailCompanyStaffService,
        ActivateCompanyStaffService $activateCompanyStaffService,
        InviteOrGetUserService $inviteUserService
    ) {
        $this->getDetailCompanyStaffService = $getDetailCompanyStaffService;
        $this->activateCompanyStaffService = $activateCompanyStaffService;
        $this->inviteUserService = $inviteUserService;
    }

    public function execute($dto = null)
    {
        $staff = $this->getDetailCompanyStaffService->execute($dto);
        $user = $this->inviteUserService->execute((object)[
            'fullName' => $staff->fullName,
            'email' => $staff->email,
        ]);
        $this->activateCompanyStaffService->execute((object)[
            'userId' => $user->id,
            'companyXid' => $dto->xid,
            'createdBy' => $dto->userId,
        ]);
    }
}
