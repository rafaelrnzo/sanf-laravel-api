<?php

namespace Sanf\Dashboard\Modules\Role\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\Role\Models\RoleEncryptedModel;

class RoleEncryptedEloquentRepository extends AbstractEloquentRepository
{
    protected RoleEncryptedModel $roleModel;

    public function __construct(RoleEncryptedModel $roleModel)
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
