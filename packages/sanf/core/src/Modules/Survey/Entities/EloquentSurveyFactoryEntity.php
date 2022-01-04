<?php

namespace Sanf\Core\Modules\Survey\Entities;

class EloquentSurveyFactoryEntity implements SurveyEntityFactoryInterface
{
    public function make(array $attributes): SurveyEntitiesInterface
    {
        return new SurveyEntity($attributes);
    }
}
