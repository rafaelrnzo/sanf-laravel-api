<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class ScaninaProductFilterDto extends ScaninaRequestDataTransferObject
{
    public string $type;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
    public ?bool $isOnlyTopCategory;
}
