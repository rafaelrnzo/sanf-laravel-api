<?php

namespace Sanf\Core\Modules\Ocr\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

final class OcrScanDocumentResponseDto extends DataTransferObject
{
    public string $invoiceNo;
    public string $invoiceDate;
    public string $invoiceAmount;
    public string $taxAmount;
    public string $vatAmount;
    public string $totalAmount;
}
