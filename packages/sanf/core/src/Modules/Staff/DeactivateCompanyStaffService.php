<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;

class DeactivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;

    /**
     * DeactivateCompanyStaffService constructor.
     * @param AuthModel $userRepository
     */
    public function __construct(AuthModel $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute($dto)
    {
        return true;
    }
}
