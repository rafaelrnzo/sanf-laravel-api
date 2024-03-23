<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductSubSpecificationResponseDto extends FlexibleDataTransferObject
{
    public ?int $id;
    public ?string $name;
    public ?string $description;
    public ?bool $value;
    public ?array $subSpecificationColumn;
}
