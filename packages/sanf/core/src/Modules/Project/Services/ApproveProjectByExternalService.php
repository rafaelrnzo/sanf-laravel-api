<?php


namespace Sanf\Core\Modules\Project\Services;


use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectApprovedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;
use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class ApproveProjectByExternalService extends ProjectService implements ApplicationServiceInterface
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
            'status_id' => ProjectStatus::PUBLISHED,
            'published_at' => Carbon::now(),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        $user = $this->userRepository->findById($project->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        event(new ProjectApprovedEvent($project, $updatedProject, $user));

        return $updatedProject;
    }
}
