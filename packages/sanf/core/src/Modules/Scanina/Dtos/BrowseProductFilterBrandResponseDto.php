<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterBrandResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $name;
    public ?string $isActive;
    public ?string $createdAt;
    public ?string $updatedAt;
}
