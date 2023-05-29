<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Sanf\Core\Modules\Scanina\Enums\ScaninaProductSortByEnum;

class BrowseCityResponseDto extends ScaninaRequestDataTransferObject
{
    public ?int $id;
    public ?string $xid;
    public ?string $name;
    public ?int $createdAt;
    public ?int $updatedAt;
}
