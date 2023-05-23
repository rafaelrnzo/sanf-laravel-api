<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterCategoryResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $parent_id;
    public ?string $level;
    public ?string $name;
    public ?string $slugName;
    public ?string $isRentActive;
    public ?string $topCategory;
    public ?string $unitTypeId;
    public ?string $unitTypeName;
    public ?string $isActive;
    public ?string $createdAt;
    public ?string $updatedAt;
}
