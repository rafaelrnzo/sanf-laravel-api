<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class GuzzleResendUserMailVerificationService implements ApplicationServiceInterface
{
    private ScaninaUserRepositoryInterface $repository;
    private ScaninaUserSpecificationInterface $specification;
    private UserRepositoryInterface $userRepository;
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProfileRepositoryInterface $profileRepository,
        ScaninaUserRepositoryInterface $repository,
        ScaninaUserSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $profile = $this->profileRepository->findById($dto->xid);
        if (is_null($profile)) {
            throw new UserNotFoundException('');
        }

        $response = $this->repository->post(
            $this->specification->resendMailVerification($profile->getEmail())
        );

        return true;
    }
}
