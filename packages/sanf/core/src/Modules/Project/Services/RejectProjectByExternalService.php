<?php

namespace Sanf\Core\Modules\Project\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectRejectedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class RejectProjectByExternalService extends ProjectService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($projectRepository);
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project)) {
            throw new GeneralProjectException('Project Not Found');
        }
        $updatedProject = $this->projectRepository->update([
            'id' => $project->id,
            'status_id' => ProjectStatus::REJECTED,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        $user = $this->userRepository->findById($project->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        event(new ProjectRejectedEvent($project, $updatedProject, $user));

        return $updatedProject;
    }
}
