<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;

class ActivateCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;

    /**
     * ActivateCompanyStaffService constructor.
     * @param AuthModel $userRepository
     */
    public function __construct(AuthModel $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute($dto)
    {
        //TODO IMPLEMENTATION
        //copy from contract owner invitation
        //create user
        //send invitation
    }
}
