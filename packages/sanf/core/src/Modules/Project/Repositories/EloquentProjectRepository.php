<?php


namespace Sanf\Core\Modules\Project\Repositories;


use NbsPhp\Core\Repositories\BaseEloquentRepository;
use Sanf\Core\Modules\Project\Models\ProjectModel;

class EloquentProjectRepository extends BaseEloquentRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(ProjectModel $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['user', 'status'])->find($id);
        return $this->stripEloquentModel($model);
    }

    public function findByXid($xid)
    {
        $model = $this->model->newQuery()->where('xid', $xid)->with(['user', 'status'])->first();
        return $this->stripEloquentModel($model);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();
        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $model = $this->model->newQuery()->forceCreate($fields);
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

    public function remove($fields, $specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->delete();
        }
        return $this->model->newQuery()->where('id', $fields['id'])->delete();
    }

    public function removeById($id)
    {
        return $this->model->newQuery()->where('id', $id)->delete();
    }

    public function removeByXid($xid)
    {
        return $this->model->newQuery()->where('xid', $xid)->delete();
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }
        return $this->model->newQuery()->select('id')->count();
    }
}
