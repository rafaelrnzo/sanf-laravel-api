<?php

namespace Sanf\Core\Modules\Financing\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingFacilityModel;

class EloquentFinancingFacilityRepository extends AbstractEloquentRepository implements FinancingFacilityRepositoryInterface
{
    protected FinancingFacilityModel $model;

    public function __construct(FinancingFacilityModel $model)
    {
        $this->model = $model;
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function first($specification)
    {
        $models = $specification->buildQuery($this->model)->first();

        return $this->stripEloquentModel($models);
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['methods'])->find($id);

        return $this->stripEloquentModel($model);
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }

        return $this->model->newQuery()->select('id')->count();
    }
}
