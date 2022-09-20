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

    public function findByXid($xid)
    {
        $models = $this->model->newQuery()
            ->where('xid', '=', $xid)
            ->first();

        return $this->stripEloquentModel($models);
    }

    public function findByUserId($userId)
    {
        $models = $this->model->newQuery()
            ->where('user_id', '=', $userId)
            ->first();

        return $this->stripEloquentModel($models);
    }

    public function create($request)
    {
        $model = $this->model->forceCreate($request);

        return $this->stripEloquentModel($model);
    }

    public function update($fields, $specification = null)
    {
        if (!is_null($specification)) {
            $model = $specification->buildQuery($this->model)->update($fields);
            return $this->stripEloquentModel($model);
        }

        $model = $this->model->newQuery()->where('id', $fields['id'])->update($fields);
        return $this->stripEloquentModel($model);
    }
}
