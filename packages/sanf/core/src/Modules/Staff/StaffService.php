<?php

namespace Sanf\Core\Modules\Staff;

use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class StaffService
{
    protected UserRepositoryInterface $userRepository;
    protected StaffRepositoryInterface $staffRepository;
    protected SanfCoreApiClient $internalApiClient;

    /**
     * ActivateCompanyStaffService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        StaffRepositoryInterface $staffRepository,
        SanfCoreApiClient $internalApiClient
    ) {
        $this->userRepository = $userRepository;
        $this->staffRepository = $staffRepository;
        $this->internalApiClient = $internalApiClient;
    }
}
