<?php

namespace Sanf\Core\Modules\RequestedDocument\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class UploadRequestedDocumentDto extends DataTransferObject
{
    public string $profile_xid;

    public string $request_id;

    public string $document_id;

    public string $origin;
    public string $filename;
}
