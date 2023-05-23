<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseProductFilterRequestDto extends ScaninaRequestDataTransferObject
{
    public int $userId;
    public int $type;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy = 'latest';
    public ?string $keyword;
}
