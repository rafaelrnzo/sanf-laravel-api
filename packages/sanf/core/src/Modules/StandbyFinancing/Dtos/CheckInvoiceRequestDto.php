<?php

namespace Sanf\Core\Modules\StandbyFinancing\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

class CheckInvoiceRequestDto extends CamelCaseDataTransferObject
{
    public string $nomorInvoice;
    public float $totalInvoice;
    public string $noplafond;
    public ?string $custId;
}
