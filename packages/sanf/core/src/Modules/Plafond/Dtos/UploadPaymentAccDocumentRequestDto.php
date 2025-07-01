<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

final class UploadPaymentAccDocumentRequestDto extends DataTransferObject
{
    public $userId;
    public $profileXid;
    public $file;
}
