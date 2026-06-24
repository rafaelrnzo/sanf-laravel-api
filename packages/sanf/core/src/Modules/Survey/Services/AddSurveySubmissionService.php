<?php

namespace Sanf\Core\Modules\Survey\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Jobs\ProcessSurveySubmissionJob;

class AddSurveySubmissionService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        dispatch(new ProcessSurveySubmissionJob($dto->toArray()));

        return true;
    }
}
