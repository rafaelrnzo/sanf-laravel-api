<?php

namespace Sanf\Core\Modules\Plafond\Dtos;

use NbsPhp\Core\Dto\CamelCaseDataTransferObject;

final class DisbursementInvoiceFormRequest extends CamelCaseDataTransferObject
{
    public $photos;
    public string $fileName;
    public string $originName;
    public string $invoiceNo;
    public string $invoiceDate;
    public float $invoiceAmount;
    public float $taxAmount;
    public float $vatAmount;
    public float $backhargeAmount;
    public float $otherAmount;
    public float $totalAmount;
    public int $orderNo;
}
