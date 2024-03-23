<?php

namespace Sanf\Core\Modules\Project\Specifications;

use Sanf\Core\Modules\Project\Models\ProjectModel;

class EloquentAllOwnedProjectSpecification
{
    private int $userId;

    /**
     * EloquentUserProjectMetadataSpecification constructor.
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function buildQuery(ProjectModel $model)
    {
        return $model->newQuery()
            ->where('user_id', $this->userId);
    }
}
