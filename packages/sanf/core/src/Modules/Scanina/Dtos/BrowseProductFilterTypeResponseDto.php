<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterTypeResponseDto extends FlexibleDataTransferObject
{

    public ?int $id;
    public ?string $xid;
    public ?int $brandId;
    public ?string $name;
    public ?bool $isActive;
    public ?int $createdAt;
    public ?int $updatedAt;
}
