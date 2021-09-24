<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectUpdatedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class RejectProjectByExternalService extends ProjectService implements ApplicationServiceInterface
{
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

        event(new ProjectUpdatedEvent($project, $updatedProject));

        return $updatedProject;
    }
}
