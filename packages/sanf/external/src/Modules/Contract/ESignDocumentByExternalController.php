<?php

namespace Sanf\External\Modules\Contract;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\Contract\Transformers\ESignDocumentCompleteTransformer;
use Sanf\Api\Modules\Contract\Transformers\ESignDocumentFailedTransformer;
use Sanf\Api\Modules\Contract\Transformers\ESignDocumentSignedTransformer;
use Sanf\Api\Modules\Contract\Transformers\ESignUserRegisteredTransformer;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentCompleteService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentFailedService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentSignedService;
use Sanf\Core\Modules\Contract\Services\ESignUserRegisteredService;
use Spatie\Fractalistic\ArraySerializer;

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
            'data.sign' => [
                'array',
                Rule::requiredIf(function () use ($request) {
                return $request->code === 'DOCUMENT_SIGNED';
            })],
            'data.sign.*.email' => [
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
            $this->postHasVerified($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGNED') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
                'email' => $input['data']['sign'][0]['email'],
            ];
            $this->postDocumentSigned($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGN_FAILED') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
            ];
            $this->postDocumentFailed($dto);
        }

        if ($input['code'] === 'DOCUMENT_SIGN_COMPLETE') {
            $dto = (object) [
                'documentId' => $input['data']['document_id'],
                'email' => $input['data']['signers'][0]['email'],
            ];
            $this->postDocumentComplete($dto);
        }

        $this->responseOk();
    }

    private function postHasVerified(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->registerService, $this->transactionalSession);
        $transactionalService->execute($dto);
    }

    private function postDocumentSigned(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->signedDocumentService, $this->transactionalSession);
        $transactionalService->execute($dto);
    }

    private function postDocumentFailed(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->failedDocumentService, $this->transactionalSession);
        $transactionalService->execute($dto);
    }

    private function postDocumentComplete(object $dto)
    {
        $transactionalService = new TransactionalApplicationService($this->completeDocumentService, $this->transactionalSession);
        $transactionalService->execute($dto);
    }
}
