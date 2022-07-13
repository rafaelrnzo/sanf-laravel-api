<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class UpdateESignDocumentStatusDto extends DataTransferObject
{
    public int $userId;
    public string $email;
    public string $documentId;
}
