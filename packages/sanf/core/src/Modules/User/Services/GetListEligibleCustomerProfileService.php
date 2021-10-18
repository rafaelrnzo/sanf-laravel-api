<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Staff\ValidateEligibleProfileByStaffService;
use Sanf\Core\Modules\User\GetListCustomerProfileService;

class GetListEligibleCustomerProfileService implements ApplicationServiceInterface
{
    protected GetListCustomerProfileService $customerProfileService;
    protected ValidateEligibleProfileByStaffService $validateEligibleProfileByStaffService;

    /**
     * GetListActiveCustomerProfileService constructor.
     * @param GetListCustomerProfileService $customerProfileService
     * @param ValidateEligibleProfileByStaffService $validateEligibleProfileByStaffService
     */
    public function __construct(GetListCustomerProfileService $customerProfileService, ValidateEligibleProfileByStaffService $validateEligibleProfileByStaffService)
    {
        $this->customerProfileService = $customerProfileService;
        $this->validateEligibleProfileByStaffService = $validateEligibleProfileByStaffService;
    }

    public function execute($dto = null)// email, userId
    {
        $customerProfiles = $this->customerProfileService->execute($dto);
        $validatedProfiles = $this->validateEligibleProfileByStaffService->execute((object)[
            'customerProfiles' => $customerProfiles,
            'userId' => $dto->userId,
        ]);

        return collect($validatedProfiles->data)
            ->filter(function ($item) {
                return $item->isEligible;
            });
    }
}
