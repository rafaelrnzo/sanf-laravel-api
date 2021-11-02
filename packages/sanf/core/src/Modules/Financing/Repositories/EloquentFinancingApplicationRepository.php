<?php


namespace Sanf\Core\Modules\Financing\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingApplicationModel;

class EloquentFinancingApplicationRepository extends AbstractEloquentRepository implements FinancingApplicationRepositoryInterface
{
    protected FinancingApplicationModel $model;

    public function __construct(FinancingApplicationModel $model)
    {
        $this->model = $model;
    }

    public function findByXid($xid)
    {
        $model = $this->model->newQuery()->where('xid', $xid)->with(['status'])->first();
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

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }
        return $this->model->newQuery()->select('id')->count();
    }
}

