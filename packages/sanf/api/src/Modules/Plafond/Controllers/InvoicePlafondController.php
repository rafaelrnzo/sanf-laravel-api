<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Plafond\Transformers\InvoicePlafondScanOcrDocumentTransformer;
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
        UploadAssetService $uploadService
    ) {
        $inputs = $this->validate($request, [
            'document' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:10000',
                function ($attribute, $document, $fail) {
                    $limit = config('ocr.document.max_page');
                    $file = file_get_contents($document);
                    $totalPage = preg_match_all("/\/Page\W/", $file);

                    if ($totalPage > $limit) {
                        $fail("{$attribute} more than {$limit} page.");
                    }
                },
            ],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'image', 'mimetypes:image/png,image/jpeg,image/jpg', 'max:5000'],
        ]);

        $documentMetadata = $uploadService->execute(new InvoicePlafondUploadDocumentRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'file' => $inputs['document'],
        ]));

        $photosMetadata = [];
        foreach ($inputs['photos'] ?? [] as $photo) {
            $photosMetadata[] = $uploadService->execute(new InvoicePlafondUploadDocumentRequestDto([
                'userId' => $auth->id(),
                'profileXid' => $xid,
                'file' => $photo,
            ]));
        }

        $uploadFile = [
            'document' => $documentMetadata,
            'photos' => $photosMetadata,
        ];

        return fractal((object) $uploadFile, InvoicePlafondUploadDocumentTransformer::class);
    }

    public function scanOCRDocument(
        Guard $auth,
        string $xid,
        Request $request,
        OcrScanDocumentService $ocrDocumentScanService
    ) {
        $this->validate($request, [
            'document' => [
                'required',
                'file',
                'mimetypes:application/pdf',
                'max:10000',
                function ($attribute, $document, $fail) {
                    $limit = config('ocr.document.max_page');
                    $file = file_get_contents($document);
                    $totalPage = preg_match_all("/\/Page\W/", $file);

                    if ($totalPage > $limit) {
                        $fail("{$attribute} more than {$limit} page.");
                    }
                },
            ],
        ]);

        $requestDto = new InvoicePlafondUploadDocumentRequestDto([
            'userId' => $auth->id(),
            'profileXid' => $xid,
            'file' => $request->file('document'),
        ]);

        $scanDocument = $ocrDocumentScanService->execute($requestDto);

        return fractal($scanDocument, InvoicePlafondScanOcrDocumentTransformer::class);
    }
}
