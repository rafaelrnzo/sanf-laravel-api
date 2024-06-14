<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\Asset\PrivateAssetFileSimpleTransformer;
use Sanf\Core\Modules\Plafond\UseCases\DownloadPaymentAccelarationDocumentUseCase;
use Sanf\Core\Modules\Plafond\UseCases\SavePaymentAccelarationDocumentUseCase;

class PlafondDocumentController extends RestApiController
{
    public function downloadPaymentAccelarationDocument(
        string $xid,
        string $plafond_xid,
        Guard $auth,
        DownloadPaymentAccelarationDocumentUseCase $downloadUseCase
    ) {
        $requestDto = (object) [
            'userId' => $auth->id(),
            'clientId' => $xid,
            'plafondId' => $plafond_xid,
        ];

        return $downloadUseCase->execute($requestDto);
    }

    public function sendPaymentAccelarationDocument(Guard $auth)
    {
        return $this->responseOk();
    }

    public function printPaymentAccelarationDocument(
        string $xid,
        string $plafond_xid,
        Request $request,
        Guard $auth,
        TransactionalSessionInterface $transactionalSession,
        SavePaymentAccelarationDocumentUseCase $saveUseCase
    ) {
        $this->validate($request, [
            'company_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'bowheer' => ['nullable', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'document_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'document_date' => ['required', 'date_format:Y-m-d'],
            'first_signer.company' => ['nullable', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'first_signer.full_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'first_signer.position' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'second_signer.company' => ['nullable', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'second_signer.full_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'second_signer.position' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'save' => ['required', 'boolean'],
        ]);

        $requestDto = (object) [
            'userId' => $auth->id(),
            'clientId' => $xid,
            'plafondId' => $plafond_xid,
            'companyName' => $request->get('company_name'),
            'bowheerName' => $request->get('bowheer') ?? null,
            'documentNo' => $request->get('document_no'),
            'documentDate' => $request->get('document_date'),
            'firstSigner' => (object) [
                'company' => $request->get('first_signer')['company'] ?? null,
                'fullName' => $request->get('first_signer')['full_name'],
                'position' => $request->get('first_signer')['position'],
            ],
            'secondSigner' => (object) [
                'company' => $request->get('second_signer')['company'] ?? null,
                'fullName' => $request->get('second_signer')['full_name'],
                'position' => $request->get('second_signer')['position'],
            ],
            'save' => $request->get('save'),
        ];

        $transactionService = new TransactionalApplicationService($saveUseCase, $transactionalSession);

        $result = $transactionService->execute($requestDto);

        return fractal($result, PrivateAssetFileSimpleTransformer::class);
    }
}
