<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadInvoiceCollectionSubmissionByUserRequestDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public int $userId;
}
