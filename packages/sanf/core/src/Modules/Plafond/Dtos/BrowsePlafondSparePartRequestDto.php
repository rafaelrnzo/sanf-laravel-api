<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class BrowsePlafondSparePartRequestDto extends CamelCaseDataTransferObject
{
    public string $profileXid;
    public ?int $skip;
    public ?int $limit;
    public ?string $sortBy;
}
