<?php

namespace Sanf\Core\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\User\UserAuthLogModel;

class EloquentUserAuthLogRepository extends AbstractEloquentRepository implements UserAuthLogRepositoryInterface
{
    private UserAuthLogModel $model;

    public function __construct(UserAuthLogModel $model)
    {
        $this->model = $model;
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function size($specification)
    {
        return $specification->buildQuery($this->model)->count();
    }
}
