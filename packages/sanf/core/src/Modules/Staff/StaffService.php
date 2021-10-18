<?php


namespace Sanf\Core\Modules\Staff;


use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class StaffService
{
    protected AuthModel $userRepository;
    protected StaffRepositoryInterface $staffRepository;
    protected InternalApiClient $internalApiClient;

    /**
     * ActivateCompanyStaffService constructor.
     * @param AuthModel $userRepository
     */
    public function __construct(
        AuthModel $userRepository,
        StaffRepositoryInterface $staffRepository,
        InternalApiClient $internalApiClient
    ) {
        $this->userRepository = $userRepository;
        $this->staffRepository = $staffRepository;
        $this->internalApiClient = $internalApiClient;
    }
}
