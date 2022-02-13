<?php

namespace Sanf\Core\Modules\Insurance\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Insurance\Repositories\InsuranceClaimSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class InsuranceClaimSubmissionByUserService
{
    protected InsuranceClaimSubmissionRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;
    protected ProfileRepositoryInterface $profileRepository;

    public function __construct(
        InsuranceClaimSubmissionRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        ProfileRepositoryInterface $profileRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    protected function findProfileOrFail($userId)
    {
        $user = $this->profileRepository->findById($userId);
        if (!$user) {
            throw new ProfileNotFoundException();
        }
        return $user;
    }
}
