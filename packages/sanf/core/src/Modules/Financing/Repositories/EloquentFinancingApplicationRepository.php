<?php

namespace Sanf\Core\Modules\Financing\Repositories;

use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingApplicationModel;
use Sanf\Core\Modules\Financing\Models\FinancingObjectModel;

class EloquentFinancingApplicationRepository extends AbstractEloquentRepository implements FinancingApplicationRepositoryInterface
{
    protected FinancingApplicationModel $model;

    public function __construct(FinancingApplicationModel $model)
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
        $model = $this->model->newQuery()->where('xid', $xid)
            ->with(['status', 'objects', 'facility', 'method'])->first();
        return $this->stripEloquentModel($model);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();
        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $model = DB::transaction(function () use ($fields) {
            $fieldFinancingObjects = $fields['financing_objects'];
            $fieldFinancingApplication = collect($fields)->except(['financing_objects'])->toArray();
            $model = $this->model->newQuery()->forceCreate($fieldFinancingApplication);
            $financingObjects = array_map(function($item) {
                return new FinancingObjectModel($item);
            }, $fieldFinancingObjects);
            $model->objects()->saveMany($financingObjects);
            return $model;
        });
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

    public function remove($specification)
    {
        return $specification->buildQuery($this->model)->delete();
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
