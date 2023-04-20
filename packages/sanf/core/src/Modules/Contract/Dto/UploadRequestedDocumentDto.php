<?php

namespace Sanf\Core\Modules\Contract\Dto;

use Spatie\DataTransferObject\DataTransferObject;

class UploadRequestedDocumentDto extends DataTransferObject
{
    public string $profile_xid;

    public string $request_id;

    public string $document_id;

    public string $origin;
    public string $filename;
}
