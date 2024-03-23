<?php

namespace Sanf\Core\Modules\Financing\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class ListFinancingMethodByFacilityRequestDto extends DataTransferObject
{
    public int $id;

    public int $limit = 10;

    public int $skip = 0;

    public ?string $sort_by;
}
