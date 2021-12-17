<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class FinancingUnitSubmissionDto extends DataTransferObject
{
    public ?string $user_id;

    public ?string $contract_no;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';
}