<?php

namespace Sanf\Core\Modules\Prepayment\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Prepayment\Repositories\PrepaymentSubmissionRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class PrepaymentSubmissionByUserService extends PrepaymentSubmissionService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(PrepaymentSubmissionRepositoryInterface $repository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($repository);
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
