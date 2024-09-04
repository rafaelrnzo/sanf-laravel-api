<?php

namespace Sanf\Core\Modules\Project\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ProjectByUserService extends ProjectService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($projectRepository);
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
