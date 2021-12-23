<?php

namespace Sanf\Core\Modules\Insurance\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Insurance\Repositories\InsuranceClaimSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class InsuranceClaimSubmissionByUserService
{
    protected InsuranceClaimSubmissionRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(InsuranceClaimSubmissionRepositoryInterface $repository, UserRepositoryInterface $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
