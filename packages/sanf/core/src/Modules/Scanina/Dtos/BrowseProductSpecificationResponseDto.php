<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class BrowseProductSpecificationResponseDto extends FlexibleDataTransferObject
{
    public ?int $id;
    public ?string $name;
    public ?array $specificationColumn;
    public ?array $subSpecification;
}
