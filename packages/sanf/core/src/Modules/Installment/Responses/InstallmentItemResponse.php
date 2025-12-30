<?php

namespace Sanf\Core\Modules\Installment\Responses;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class InstallmentItemResponse extends CamelCaseDataTransferObject
{
    public ?string $contractNo;
    public ?string $financingTypeId;
    public ?string $financingTypeDescription;
    public ?float $totalAmount;
    public ?int $dueDate;
    public ?string $status;
    public ?string $paymentXid;
    public ?int $sequenceNumber;
    public ?int $sequenceTotal;
}
