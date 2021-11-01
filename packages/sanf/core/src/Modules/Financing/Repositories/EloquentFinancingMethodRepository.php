<?php


namespace Sanf\Core\Modules\Financing\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Financing\Models\FinancingMethodModel;

class EloquentFinancingMethodRepository extends AbstractEloquentRepository implements FinancingMethodRepositoryInterface
{
    protected FinancingMethodModel $model;

    public function __construct(FinancingMethodModel $model)
    {
        $this->model = $model;
    }

    public function find($limit, $offset, $sort_by)
    {
        $models = $this->model
            ->newQuery()
            ->select([
                'id',
                'name',
            ])
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', $sort_by)
            ->get();

        return $this->stripEloquentModel($models);
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

