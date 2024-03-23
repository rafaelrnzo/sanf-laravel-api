<?php

namespace Sanf\Core\Modules\Survey\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateAssigneeSurveyDto extends CamelCaseDataTransferObject
{
    public int $userId;
}
