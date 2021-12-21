<?php

namespace Sanf\Core\Modules\Contract\Services;

use Sanf\Core\Modules\Contract\Repositories\FinancingUnitLocationSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class FinancingUnitLocationSubmissionByUserService
{
    protected FinancingUnitLocationSubmissionRepositoryInterface $repository;
    protected UserRepositoryInterface $userrepository;

    public function __construct(FinancingUnitLocationSubmissionRepositoryInterface $repository, UserRepositoryInterface $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }
}
