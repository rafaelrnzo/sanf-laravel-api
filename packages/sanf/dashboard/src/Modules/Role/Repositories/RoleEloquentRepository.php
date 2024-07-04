<?php

namespace Sanf\Dashboard\Modules\Role\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\Role\Models\RoleModel;

class RoleEloquentRepository extends AbstractEloquentRepository
{
    protected RoleModel $roleModel;

    public function __construct(RoleModel $roleModel)
    {
        $this->roleModel = $roleModel;
    }

    public function findByEntity(int $entityId)
    {
        $roleModel = $this->roleModel
            ->newQuery()
            ->where('entityTypeId', '=', $entityId)
            ->first();

        return $this->stripEloquentModel($roleModel);
    }
}
