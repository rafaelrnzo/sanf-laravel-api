<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

class BrowseCityResponseDto extends ScaninaRequestDataTransferObject
{
    public ?int $id;
    public ?string $xid;
    public ?string $name;
    public ?int $createdAt;
    public ?int $updatedAt;
}
