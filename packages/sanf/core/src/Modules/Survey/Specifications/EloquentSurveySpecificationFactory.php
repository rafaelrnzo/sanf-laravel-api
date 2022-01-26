<?php

namespace Sanf\Core\Modules\Survey\Specifications;

class EloquentSurveySpecificationFactory implements SurveySpecificationFactoryInterface
{
    public function getAll()
    {
        return new EloquentAllSpecification();
    }
}
