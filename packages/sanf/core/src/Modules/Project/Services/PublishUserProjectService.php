<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class PublishUserProjectService extends ProjectService implements ApplicationServiceInterface
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
        return $this->projectRepository->update([
            'id' => $project->id,
            'status_id' => ProjectStatus::PUBLISHED,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);
        //TODO USER LOGGING USING EVENT
    }
}
