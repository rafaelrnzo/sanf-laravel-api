<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Plafond\Transformers\InvoicePlafondUploadDocumentTransformer;
use Sanf\Core\Modules\Asset\UploadAssetService;
use Sanf\Core\Modules\Ocr\Services\OcrScanDocumentService;
use Sanf\Core\Modules\Plafond\Dtos\InvoicePlafondUploadDocumentRequestDto;

class InvoicePlafondController extends RestApiController
{
    public function uploadDocument(
        Guard $auth,
        string $xid,
        Request $request,
        UploadAssetService $uploadService,
        OcrScanDocumentService $ocrDocumentScanService
    ) {
        $this->validate($request, [
            'file' => ['required', 'file', 'mimetypes:application/pdf', 'max:10000'],
            'ocr_scan' => ['nullable', 'boolean'],
        ]);

        $requestDto = new InvoicePlafondUploadDocumentRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'file' => $request->file('file'),
            'ocrScan' => $request->get('ocr_scan'),
        ]);

        $uploadServiceResult = $uploadService->execute($requestDto);

        $scanDocumentServiceResult = $ocrDocumentScanService->execute($requestDto);

        $result = $uploadServiceResult->toArray() + $scanDocumentServiceResult->toArray();

        return fractal((object) $result, InvoicePlafondUploadDocumentTransformer::class);
    }
}
