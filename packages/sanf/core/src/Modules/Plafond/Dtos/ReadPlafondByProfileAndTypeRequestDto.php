<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ReadPlafondByProfileAndTypeRequestDto extends DataTransferObject
{
    public int $userId;
    public string $profileXid;
    public string $typeId;
}
