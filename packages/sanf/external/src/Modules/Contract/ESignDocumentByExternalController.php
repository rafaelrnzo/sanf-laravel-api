<?php

namespace Sanf\External\Modules\Contract;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use Sanf\Api\Modules\Contract\Transformers\ESignDocumentCompleteTransformer;
use Sanf\Api\Modules\Contract\Transformers\ESignDocumentSignedTransformer;
use Sanf\Api\Modules\Contract\Transformers\ESignUserRegisteredTransformer;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentCompleteService;
use Sanf\Core\Modules\Contract\Services\ESignUserDocumentSignedService;
use Spatie\Fractalistic\ArraySerializer;

class ESignDocumentByExternalController extends RestApiController
{
    public function postHasVerified(
        Request $request,
        ESignUserDocumentCompleteService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input  = $this->validate($request, [
            'email' => 'required|email|string|max:255',
        ]);

        $dto = (object) ['email' => $input['email']];

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $result = $transactionalService->execute($dto);
        $result->response_code = 'REGISTRATION_COMPLETE';

        return fractal($result, ESignUserRegisteredTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }

    public function postDocumentSigned(
        Request $request,
        ESignUserDocumentSignedService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input  = $this->validate($request, [
            'email' => 'required|email|string|max:255',
            'document_id' => 'required|string|max:255',
        ]);

        $dto = (object) [
            'email' => $input['email'],
            'documentId' => $input['document_id'],
        ];

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $result = $transactionalService->execute($dto);
        $result->response_code = 'DOCUMENT_SIGNED';

        return fractal($result, ESignDocumentSignedTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }

    public function postDocumentComplete(
        Request $request,
        ESignUserDocumentCompleteService $service,
        TransactionalSessionInterface $transactionalSession
    ) {
        $input  = $this->validate($request, [
            'email' => 'required|email|string|max:255',
            'document_id' => 'required|string|max:255',
        ]);

        $dto = (object) [
            'email' => $input['email'],
            'documentId' => $input['document_id'],
        ];

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $result = $transactionalService->execute($dto);
        $result->response_code = 'DOCUMENT_SIGN_COMPLETE';

        return fractal($result, ESignDocumentCompleteTransformer::class)
            ->serializeWith(ArraySerializer::class);
    }
}
