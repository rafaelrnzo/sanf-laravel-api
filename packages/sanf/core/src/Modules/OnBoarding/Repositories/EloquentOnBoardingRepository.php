<?php

namespace Sanf\Core\Modules\OnBoarding\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\OnBoarding\Models\OnBoardingModel;

class EloquentOnBoardingRepository extends AbstractEloquentRepository implements OnBoardingRepositoryInterface
{
    private OnBoardingModel $model;

    public function __construct(OnBoardingModel $model)
    {
        $this->model = $model;
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function update($id, $request)
    {
        $this->findById($id)->update($request);
        $model = $this->findById($id);

        return $this->stripEloquentModel($model);
    }
}
