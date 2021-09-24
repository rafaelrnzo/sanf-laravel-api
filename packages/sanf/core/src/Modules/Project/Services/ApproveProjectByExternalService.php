<?php


namespace Sanf\Core\Modules\Project\Services;


use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectApprovedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class ApproveProjectByExternalService extends ProjectService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project)) {
            throw new GeneralProjectException('Project Not Found');
        }
        $updatedProject =  $this->projectRepository->update([
            'id' => $project->id,
            'status_id' => ProjectStatus::PUBLISHED,
            'published_at' => Carbon::now(),
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new ProjectApprovedEvent($project, $updatedProject));

        return $updatedProject;
    }
}
