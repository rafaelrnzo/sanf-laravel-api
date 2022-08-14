<?php

namespace Sanf\Core\Modules\Setting\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Setting\Models\FaqCategoryModel;

class EloquentFaqCategoryRepository extends AbstractEloquentRepository implements FaqCategoryRepositoryInterface
{
    private FaqCategoryModel $model;

    public function __construct(FaqCategoryModel $model)
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

    public function create($request)
    {
        $model = $this->model->forceCreate($request);

        return $this->stripEloquentModel($model);
    }

    public function update($id, $request)
    {
        $this->findById($id)->update($request);
        $model = $this->findById($id);

        return $this->stripEloquentModel($model);
    }

    public function destroy($id)
    {
        return $this->findById($id)->delete();
    }
}
