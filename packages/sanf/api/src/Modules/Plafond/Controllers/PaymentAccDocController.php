<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Plafond\Transformers\ScanOcrPaymentAccDocumentTransformer;
use Sanf\Core\Modules\Ocr\Exceptions\DocumentScanLimitException;
use Sanf\Core\Modules\Ocr\Services\OcrScanPaymentAccDocumentService;
use Sanf\Core\Modules\Plafond\Dtos\UploadPaymentAccDocumentRequestDto;

class PaymentAccDocController extends RestApiController
{
    public function scanOCRUploadDocument(
        Guard $auth,
        string $xid,
        Request $request,
        OcrScanPaymentAccDocumentService $ocrPaymentAccDocumentScanService
    ) {
        $this->validate($request, [
            'document' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:10000',
            ],
        ]);

        $limit = config('ocr.document.max_page');
        $document = $request->file('document');
        $file = file_get_contents($document);
        $totalPage = preg_match_all("/\/Page\W/", $file);

        if ($totalPage > $limit) {
            throw new DocumentScanLimitException();
        }

        $requestDto = new UploadPaymentAccDocumentRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'file' => $document,
        ]);

        $scanDocument = $ocrPaymentAccDocumentScanService->execute($requestDto);

        return fractal($scanDocument, ScanOcrPaymentAccDocumentTransformer::class);
    }
}
