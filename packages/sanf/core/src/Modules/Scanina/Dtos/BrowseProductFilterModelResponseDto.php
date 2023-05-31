<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductFilterModelResponseDto extends FlexibleDataTransferObject
{

    public ?int $id;
    public ?string $xid;
    public ?int $typeId;
    public ?string $name;
    public ?bool $isActive;
    public ?int $createdAt;
    public ?int $updatedAt;
}
