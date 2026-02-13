<?php

namespace Sanf\Core\Modules\Contract\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ESignDocumentCheckStatusRequestDto extends DataTransferObject
{
    public int $userId;
    public string $sanfId;
    public string $documentId;
}
