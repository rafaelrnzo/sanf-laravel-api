<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class CountESignDocumentAssigneeStatusRequestDto extends DataTransferObject
{
    public ?int $userId;
    public ?string $sanfId;
}
