<?php

namespace Sanf\Core\Modules\Setting\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Setting\Models\FrequentlyAskQuestionCategoryModel;
use Sanf\Core\Modules\Setting\Models\FrequentlyAskQuestionModel;

class EloquentFrequentlyAskQuestionRepository extends AbstractEloquentRepository implements FrequentlyAskQuestionRepositoryInterface
{
    private FrequentlyAskQuestionModel $model;
    private FrequentlyAskQuestionCategoryModel $categoryModel;

    public function __construct(FrequentlyAskQuestionModel $model, FrequentlyAskQuestionCategoryModel $categoryModel)
    {
        $this->model = $model;
        $this->categoryModel = $categoryModel;
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

    public function queryCategory($specification)
    {
        $models = $specification->buildQuery($this->categoryModel)->get();

        return $this->stripEloquentModel($models);
    }

    public function sizeCategory($specification)
    {
        return $specification->buildQuery($this->categoryModel)->count();
    }

    public function findCategoryById($id)
    {
        return $this->categoryModel->newQuery()->find($id);
    }

    public function createCategory($request)
    {
        $model = $this->categoryModel->forceCreate($request);

        return $this->stripEloquentModel($model);
    }

    public function updateCategory($id, $request)
    {
        $this->findCategoryById($id)->update($request);
        $model = $this->findCategoryById($id);

        return $this->stripEloquentModel($model);
    }

    public function destroyCategory($id)
    {
        return $this->findCategoryById($id)->delete();
    }
}
