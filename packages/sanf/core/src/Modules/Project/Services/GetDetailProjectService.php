<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\GeneralProjectException;
use Sanf\Core\Modules\Project\ProjectStatus;

class GetDetailProjectService extends ProjectService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->status_id != ProjectStatus::PUBLISHED) {
            throw new GeneralProjectException('Project Not Found');
        }
        $project->is_owner = ($project->user_id == $dto->userId);

        return $project;
    }
}
