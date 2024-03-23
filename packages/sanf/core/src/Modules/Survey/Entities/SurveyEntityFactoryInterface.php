<?php

namespace Sanf\Core\Modules\Survey\Entities;

interface SurveyEntityFactoryInterface
{
    public function make(array $attributes): SurveyEntitiesInterface;
}
