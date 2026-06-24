<?php

namespace Sanf\Core\Modules\Survey\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddSurveySubmissionRequestDto extends CamelCaseDataTransferObject
{
    public string $profileXid;

    public string $branchId;

    public string $contractNo;

    public ?string $picName;

    public ?string $customerName;

    public ?string $projectName;

    public ?string $projectId;

    public ?string $segment;

    public string $surveyDate;

    public array $items;

    public ?string $xid;
}
