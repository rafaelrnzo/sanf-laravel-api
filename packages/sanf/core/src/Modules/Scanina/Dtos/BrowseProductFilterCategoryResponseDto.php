<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterCategoryResponseDto extends FlexibleDataTransferObject
{

    public ?int $id;
    public ?string $xid;
    public ?int $parent_id;
    public ?int $level;
    public ?string $name;
    public ?string $slugName;
    public ?bool $isRentActive;
    public ?bool $topCategory;
    public ?int $unitTypeId;
    public ?string $unitTypeName;
    public ?bool $isActive;
    public ?int $createdAt;
    public ?int $updatedAt;
}
