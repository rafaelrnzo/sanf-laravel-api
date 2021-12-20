<?php

namespace Sanf\Core\Modules\Invoice\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class ReadInvoiceCollectionSubmissionByUserResponseDto extends CamelCaseDataTransferObject
{
    public int $id;
    public string $xid;
    public int $userId;
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
}
