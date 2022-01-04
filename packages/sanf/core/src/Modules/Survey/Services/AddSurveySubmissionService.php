<?php

namespace Sanf\Core\Modules\Survey\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Survey\Dtos\FormAddSurveyByUserDto;
use Sanf\Core\Modules\Survey\Jobs\SubmitCoreSurveySubmissionJob;
use Sanf\Core\Modules\Survey\Jobs\SubmitSurveySubmissionJob;

class AddSurveySubmissionService implements ApplicationServiceInterface
{
    /**
     * @param FormAddSurveyByUserDto|null $dto
     * @return bool
     */
    public function execute($dto = null)
    {
        dispatch(new SubmitSurveySubmissionJob($dto));
        dispatch(new SubmitCoreSurveySubmissionJob($dto));

        return true;
    }
}
