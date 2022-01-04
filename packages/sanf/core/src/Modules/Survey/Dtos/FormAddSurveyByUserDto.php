<?php

namespace Sanf\Core\Modules\Survey\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class FormAddSurveyByUserDto extends CamelCaseDataTransferObject
{
    public string $profileXid;

    public string $branchId;

    public string $contractNo;

    public ?string $companyName;

    public ?string $customerName;

    public ?string $projectName;

    public ?string $segment;

    public array $items;

    public ?string $xid;
}
