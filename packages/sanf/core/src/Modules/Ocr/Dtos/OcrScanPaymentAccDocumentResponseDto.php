<?php

namespace Sanf\Core\Modules\Ocr\Dtos;

use Sanf\Core\Modules\Asset\AssetUploadResultDto;

final class OcrScanPaymentAccDocumentResponseDto extends AssetUploadResultDto
{
    /**
     * scanned from invoice_id.
     */
    public string $documentNo;

    /**
     * scanned from invoice_date.
     */
    public string $documentDate;
}
