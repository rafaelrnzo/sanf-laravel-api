<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterTypeResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $brandId;
    public ?string $name;
    public ?string $isActive;
    public ?string $createdAt;
    public ?string $updatedAt;
}
