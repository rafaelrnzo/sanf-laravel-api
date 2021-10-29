<?php


namespace Sanf\Core\Modules\Financing\Dto;


use Spatie\DataTransferObject\DataTransferObject;

class ListFinancingPrerequisiteRequestDto extends DataTransferObject
{
    public int $limit = 10;

    public int $skip = 0;

    public ?string $sort_by;
}
