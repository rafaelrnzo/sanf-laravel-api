<?php

namespace Sanf\Core\Modules\Survey\Enums;

use MyCLabs\Enum\Enum;

class SurveyStatusEnum extends Enum
{
    public const SUBMIT = 1;
    public const FINISHED = 2;

    public function getTranslation()
    {
        return __('core::constant.survey.' . $this->getKey());
    }
}
