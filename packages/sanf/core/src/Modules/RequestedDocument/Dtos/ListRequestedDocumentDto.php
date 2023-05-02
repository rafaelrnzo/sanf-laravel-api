<?php

namespace Sanf\Core\Modules\RequestedDocument\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class ListRequestedDocumentDto extends DataTransferObject
{
    public ?int $user_id;

    public string $profile_xid;

    public ?string $document_type;
    public ?string $status;

    public int $skip = 0;

    public int $limit = 10;

    public string $sort_by = 'earliest';
    public ?string $keyword;
}
