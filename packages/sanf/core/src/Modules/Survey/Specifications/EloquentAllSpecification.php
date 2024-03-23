<?php

namespace Sanf\Core\Modules\Survey\Specifications;

use Sanf\Core\Modules\Survey\Models\SurveyModel;

class EloquentAllSpecification
{
    public function buildQuery(SurveyModel $model)
    {
        return $model->newQuery();
    }
}
