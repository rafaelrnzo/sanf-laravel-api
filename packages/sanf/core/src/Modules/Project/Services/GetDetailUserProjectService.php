<?php


namespace Sanf\Core\Modules\Project\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Project\GeneralProjectException;

class GetDetailUserProjectService extends ProjectService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $project = $this->projectRepository->findByXid($dto->xid);
        if (is_null($project) || $project->user_id != $user->id) {
            throw new GeneralProjectException('Project Not Found');
        }
        return $project;
    }
}
