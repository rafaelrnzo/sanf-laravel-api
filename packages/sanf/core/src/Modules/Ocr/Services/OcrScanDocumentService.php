<?php

namespace Sanf\Core\Modules\Ocr\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Ocr\Dtos\OcrScanDocumentResponseDto;
use Sanf\Core\Modules\Plafond\Dtos\InvoicePlafondUploadDocumentRequestDto;
use Sanf\Integration\Modules\Nanonets\NanonetsClient;

final class OcrScanDocumentService implements ApplicationServiceInterface
{
    protected NanonetsClient $client;

    public function __construct(NanonetsClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto = null)
    {
        /** @var InvoicePlafondUploadDocumentRequestDto $dto */
        if ($dto->ocrScan === false) {
            return new OcrScanDocumentResponseDto([
            'invoiceNo' => '',
            'invoiceDate' => '',
            'invoiceAmount' => '',
            'taxAmount' => '',
            'vatAmount' => '',
            'totalAmount' => '',
            ]);
        }

        $uploadResponse = $this->client->scanDocument($dto->file);

        return new OcrScanDocumentResponseDto([
            'invoiceNo' => '123ABC',
            'invoiceDate' => '123ABC',
            'invoiceAmount' => '123ABC',
            'taxAmount' => '123ABC',
            'vatAmount' => '123ABC',
            'totalAmount' => '123ABC',
        ]);
    }
}
