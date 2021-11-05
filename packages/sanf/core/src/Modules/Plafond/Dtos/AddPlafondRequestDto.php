<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class AddPlafondRequestDto extends DataTransferObject
{
    public string $plafondTypeId;
    public string $amount;
}
