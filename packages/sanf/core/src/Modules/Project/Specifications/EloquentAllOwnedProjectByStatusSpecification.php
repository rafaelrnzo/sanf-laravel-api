<?php

namespace Sanf\Core\Modules\Project\Specifications;

use Sanf\Core\Modules\Project\Models\ProjectModel;

class EloquentAllOwnedProjectByStatusSpecification
{
    private int $userId;
    private array $statuses;

    /**
     * EloquentAllOwnedProjectByStatusSpecification constructor.
     * @param int $userId
     * @param array $status
     */
    public function __construct(int $userId, array $statuses = [])
    {
        $this->userId = $userId;
        $this->statuses = $statuses;
    }

    public function buildQuery(ProjectModel $model)
    {
        return $model->newQuery()
            ->where('user_id', $this->userId)
            ->whereIn('status_id', $this->statuses);
    }
}
