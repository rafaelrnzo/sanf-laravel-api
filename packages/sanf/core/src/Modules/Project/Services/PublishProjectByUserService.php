<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectPublishedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class PublishProjectByUserService extends ProjectByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->user_id != $user->id) {
            throw new GeneralProjectException('Project Not Found');
        }
        if ($project->status_id !== ProjectStatus::UNPUBLISHED) {
            throw new GeneralProjectException('Invalid State');
        }
        $updatedProject = $this->projectRepository->update([
            'id' => $project->id,
            'status_id' => ProjectStatus::PUBLISHED,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new ProjectPublishedEvent($project, $updatedProject));

        return $updatedProject;
    }
}
