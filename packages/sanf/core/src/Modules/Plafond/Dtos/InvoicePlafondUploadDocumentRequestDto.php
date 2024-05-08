<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

final class InvoicePlafondUploadDocumentRequestDto extends DataTransferObject
{
    public $userId;
    public $profileXid;
    public $file;
}
