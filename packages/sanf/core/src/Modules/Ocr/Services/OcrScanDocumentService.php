<?php

namespace Sanf\Core\Modules\Ocr\Services;

use Exception;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Ocr\Dtos\OcrScanDocumentResponseDto;
use Sanf\Core\Modules\Plafond\Dtos\InvoicePlafondUploadDocumentRequestDto;
use Sanf\Integration\Modules\Nanonets\NanonetsClient;
use Sanf\Integration\Modules\Fineksi\FineksiClient;

final class OcrScanDocumentService implements ApplicationServiceInterface
{
    //protected NanonetsClient $client;
    protected FineksiClient $client;

    public function __construct(FineksiClient $client)
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
	    $headerData = [];
            $financialData = [];
            foreach ($uploadResponse->data->documents as $key => $value) {
                $headerData = $value->header->invoice_details;
                $financialData = $value->header->financial_details;
            }

            return new OcrScanDocumentResponseDto([
                'invoiceNo' => $headerData->invoice_id ?? '',
                'invoiceDate' => $headerData->invoice_date ?? '',
                'invoiceAmount' => strval($financialData->subtotal_amount ?? ''),
                'taxAmount' => strval($financialData->income_tax_amount ?? ''),
                'vatAmount' => strval($financialData->total_tax_amount ?? ''),
                'backhargeAmount' => '', //$filterScannerData['backcharge'] ?? '',
                'otherAmount' => '', //$filterScannerData['others'] ?? '',
                'totalAmount' => strval($financialData->total_amount ?? ''),
            ]);

	    /*Old Script
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
                'backhargeAmount' => $filterScannerData['backcharge'] ?? '',
                'otherAmount' => $filterScannerData['others'] ?? '',
                'totalAmount' => $filterScannerData['total_after_tax'] ?? '',
            ]);*/
        } catch (Exception $exception) {
            report($exception->getMessage());
        }

        return $ocrScanDocumentResponseDto;
    }
}
