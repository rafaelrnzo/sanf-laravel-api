<?php

namespace Sanf\Core\Modules\Survey\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class PaginateSurveyByUserDto extends CamelCaseDataTransferObject
{
    public int $userId;

    public int $skip = 0;

    public int $limit = 10;

    public string $sortBy = 'earliest';

    public ?int $statusId;

    public ?string $contractNo;
}