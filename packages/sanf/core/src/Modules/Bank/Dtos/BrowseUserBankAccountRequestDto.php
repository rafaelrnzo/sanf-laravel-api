<?php

namespace Sanf\Core\Modules\Bank\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class BrowseUserBankAccountRequestDto extends DataTransferObject
{
    public int $userId;
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
    public ?string $keyword;
}
