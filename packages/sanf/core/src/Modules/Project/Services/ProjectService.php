<?php

namespace Sanf\Core\Modules\Project\Services;

use Sanf\Core\Modules\Project\Repositories\ProjectRepositoryInterface;

class ProjectService
{
    protected ProjectRepositoryInterface $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }
}
