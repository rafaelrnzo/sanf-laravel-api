<?php

namespace Sanf\Core\Modules\Project\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class ProjectByUserService extends ProjectService
{
    protected AuthModel $userRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, AuthModel $userRepository)
    {
        parent::__construct($projectRepository);
        $this->userRepository = $userRepository;
    }

    protected function findUserOrFail($userId)
    {
        $user = $this->userRepository->newQuery()->find($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
