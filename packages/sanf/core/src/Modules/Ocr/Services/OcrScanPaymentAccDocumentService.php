<?php

namespace Sanf\Core\Modules\Ocr\Services;

use Exception;
use Illuminate\Support\Arr;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Asset\AssetUploadRequestDto;
use Sanf\Core\Modules\Asset\AssetUploadResultDto;
use Sanf\Core\Modules\Asset\UploadAssetService;
use Sanf\Core\Modules\Ocr\Dtos\OcrScanPaymentAccDocumentResponseDto;
use Sanf\Core\Modules\Plafond\Dtos\UploadPaymentAccDocumentRequestDto;
use Sanf\Integration\Modules\Fineksi\FineksiClient;

final class OcrScanPaymentAccDocumentService implements ApplicationServiceInterface
{
    protected FineksiClient $client;
    protected UploadAssetService $uploadService;

    public function __construct(
        FineksiClient $client,
        UploadAssetService $uploadService
    ) {
        $this->client = $client;
        $this->uploadService = $uploadService;
    }

    /**
     * @param ?UploadPaymentAccDocumentRequestDto $dto
     * @return OcrScanPaymentAccDocumentResponseDto
     */
    public function execute($dto = null)
    {
        $invoiceDetails = null;

        try {
            $uploadResponse = $this->client->scanDocument($dto->file);
            $invoiceDetails = optional(Arr::first($uploadResponse->data->documents))->header->invoice_details;
        } catch (Exception $exception) {
            report($exception);
        }

        // set upload file dto;
        $uploadDto = new AssetUploadRequestDto([
            'file' => $dto->file,
            'type' => 3, // See AssetFileController::validating()
        ]);

        // run service;
        /** @var AssetUploadResultDto */
        $uploadResult = $this->uploadService->execute($uploadDto);

        $ocrScanDocumentResponseDto = new OcrScanPaymentAccDocumentResponseDto([
            // AssetUploadResultDto
            'path' => $uploadResult->path,
            'originName' => $uploadResult->originName,
            'fileName' => $uploadResult->fileName,
            'url' => $uploadResult->url,
            // From Scan
            'documentNo' => $invoiceDetails->invoice_id ?? '',
            'documentDate' => $invoiceDetails->invoice_date ?? '',
        ]);

        return $ocrScanDocumentResponseDto;
    }
}
