<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterModelResponseDto extends FlexibleDataTransferObject
{

    public ?string $id;
    public ?string $typeId;
    public ?string $name;
    public ?string $isActive;
    public ?string $createdAt;
    public ?string $updatedAt;
}
