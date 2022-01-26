<?php

namespace Sanf\Core\Modules\Survey\Repositories;

interface SurveyRepositoryInterface
{
    public function query($specification);
    public function add($fields);
}