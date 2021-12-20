<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class AddInvoiceCollectionSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $userId;
    public string $profileXid;
}
