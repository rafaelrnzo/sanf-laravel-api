<?php

namespace Sanf\External\Modules\Contract;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentCompleteService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentFailedService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentSignedService;
use Sanf\Core\Modules\Contract\Services\ESignUserRegisteredService;

class ESignDocumentByExternalController extends RestApiController
{
    private TransactionalSessionInterface $transactionalSession;
    private ESignUserRegisteredService $registerService;
    private ESignUserDocumentSignedService $signedDocumentService;
    private ESignUserDocumentFailedService $failedDocumentService;
    private ESignUserDocumentCompleteService $completeDocumentService;

    public function __construct(
        TransactionalSessionInterface $transactionalSession,
        ESignUserRegisteredService $registerService,
        ESignUserDocumentSignedService $signedDocumentService,
        ESignUserDocumentFailedService $failedDocumentService,
        ESignUserDocumentCompleteService $completeDocumentService
    ) {
        parent::__construct();
        $this->transactionalSession = $transactionalSession;
        $this->registerService = $registerService;
        $this->signedDocumentService = $signedDocumentService;
        $this->failedDocumentService = $failedDocumentService;
        $this->completeDocumentService = $completeDocumentService;
    }

    public function postCallback(Request $request)
    {
        $input  = $this->validate($request, [
            'status' => 'required|bool',
            'code' => 'required|string|in:REGISTRATION_COMPLETE,DOCUMENT_SIGNED,DOCUMENT_SIGN_FAILED,DOCUMENT_SIGN_COMPLETE',
            'data' => 'required',
            'data.email' => [
                'email',
                Rule::requiredIf(function () use ($request) {
                return $request->code === 'REGISTRATION_COMPLETE';
            })],
            'data.document_id' => Rule::requiredIf(function () use ($request) {
                return in_array($request->code, ['DOCUMENT_SIGNED','DOCUMENT_SIGN_FAILED','DOCUMENT_SIGN_COMPLETE']);
            }),
            'data.signer_email' => [
                'email',
                Rule::requiredIf(function () use ($request) {
                return $request->code === 'DOCUMENT_SIGNED';
            })],
            'data.signers' => [
                'array',
                Rule::requiredIf(function () use ($request) {
                return $request->code === 'DOCUMENT_SIGN_COMPLETE';
            })],
            'data.signers.*.email' => [
                'email',
                Rule::requiredIf(function () use ($request) {
                    return $request->code === 'DOCUMENT_SIGN_COMPLETE';
                })],
        ]);

        if ($input['code'] === 'REGISTRATION_COMPLETE') {
            $dto = (object) [
                'email' => $input['data']['email']
            ];
            $user = $this->postHasVerified($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGNED') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
                'email' => $input['data']['signer_email'],
            ];
            $document = $this->postDocumentSigned($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGN_FAILED') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
            ];
            $document = $this->postDocumentFailed($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGN_COMPLETE') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
                'email' => $input['data']['signers'][0]['email'],
            ];
            $document = $this->postDocumentComplete($dto);
        }

        return $this->responseOk();
    }

    private function postHasVerified(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->registerService, $this->transactionalSession);
        return $transactionalService->execute($dto);
    }

    private function postDocumentSigned(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->signedDocumentService, $this->transactionalSession);
        return $transactionalService->execute($dto);
    }

    private function postDocumentFailed(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->failedDocumentService, $this->transactionalSession);
        return $transactionalService->execute($dto);
    }

    private function postDocumentComplete(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->completeDocumentService, $this->transactionalSession);
        return $transactionalService->execute($dto);
    }
}
