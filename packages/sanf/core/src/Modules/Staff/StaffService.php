<?php


namespace Sanf\Core\Modules\Staff;


use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class StaffService
{
    protected AuthModel $userRepository;
    protected StaffRepositoryInterface $staffRepository;
    protected SanfCoreApiClient $internalApiClient;

    /**
     * ActivateCompanyStaffService constructor.
     * @param AuthModel $userRepository
     */
    public function __construct(
        AuthModel $userRepository,
        StaffRepositoryInterface $staffRepository,
        SanfCoreApiClient $internalApiClient
    ) {
        $this->userRepository = $userRepository;
        $this->staffRepository = $staffRepository;
        $this->internalApiClient = $internalApiClient;
    }
}
