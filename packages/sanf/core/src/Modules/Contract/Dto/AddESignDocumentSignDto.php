<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class AddESignDocumentSignDto extends DataTransferObject
{
    public int $userId;
    public string $email;
    public string $documentId;
    public ?string $documentName;
    public ?int $expiredAt;
}
