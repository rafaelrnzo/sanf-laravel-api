<?php

namespace Sanf\Core\Modules\Project\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\Events\ProjectUnpublishedEvent;
use Sanf\Core\Modules\Project\Exceptions\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class UnpublishProjectByUserService extends ProjectByUserService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->user_id != $user->id) {
            throw new GeneralProjectException('Project Not Found');
        }
        $updatedProject = $this->projectRepository->update([
            'id' => $project->id,
            'status_id' => ProjectStatus::UNPUBLISHED,
            'published_at' => null,
//            'modified_by' => //TODO USER SNAPSHOT
        ]);

        event(new ProjectUnpublishedEvent($project, $updatedProject));

        return $updatedProject;
    }
}
