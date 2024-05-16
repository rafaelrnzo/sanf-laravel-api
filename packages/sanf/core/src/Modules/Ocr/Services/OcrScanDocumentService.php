<?php

namespace Sanf\Core\Modules\Ocr\Services;

use Exception;
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
        $ocrScanDocumentResponseDto = new OcrScanDocumentResponseDto([
            'invoiceNo' => '',
            'invoiceDate' => '',
            'invoiceAmount' => '',
            'taxAmount' => '',
            'vatAmount' => '',
            'backhargeAmount' => '',
            'otherAmount' => '',
            'totalAmount' => '',
        ]);

        try {
            $uploadResponse = $this->client->scanDocument($dto->file);
            $latestDataResult = $uploadResponse->result;

            $filterScannerData = [];
            foreach ($latestDataResult[0]->prediction as $key => $scanner) {
                $filterScannerData[$scanner->label] = $scanner->ocr_text;
            }

            return new OcrScanDocumentResponseDto([
                'invoiceNo' => $filterScannerData['invoice_number'] ?? '',
                'invoiceDate' => $filterScannerData['invoice_date'] ?? '',
                'invoiceAmount' => $filterScannerData['subtotal_before_tax'] ?? '',
                'taxAmount' => $filterScannerData['pph23'] ?? '',
                'vatAmount' => $filterScannerData['vat_amount'] ?? '',
                'backhargeAmount' => $filterScannerData['backharge_amount'] ?? '',
                'otherAmount' => $filterScannerData['other_amount'] ?? '',
                'totalAmount' => $filterScannerData['total_after_tax'] ?? '',
            ]);
        } catch (Exception $exception) {
            report($exception->getMessage());
        }

        return $ocrScanDocumentResponseDto;
    }
}
