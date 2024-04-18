<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class BrowsePlafondFactoringRequestDto extends DataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
