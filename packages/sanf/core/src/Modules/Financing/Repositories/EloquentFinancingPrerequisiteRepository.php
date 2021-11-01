<?php


namespace Sanf\Core\Modules\Financing\Repositories;


use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingPrerequisiteModel;

class EloquentFinancingPrerequisiteRepository extends AbstractEloquentRepository implements FinancingPrerequisiteRepositoryInterface
{
    protected FinancingPrerequisiteModel $model;

    public function __construct(FinancingPrerequisiteModel $model)
    {
        $this->model = $model;
    }

    public function get($specification)
    {
        $models = $specification->buildQuery($this->model)->first();
        return $this->stripEloquentModel($models);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();
        return $this->stripEloquentModel($models);
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }
        return $this->model->newQuery()->select('id')->count();
    }

}
