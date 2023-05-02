<?php

namespace Sanf\Api\Modules\RequestedDocument\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Database\TransactionalSessionInterface;
use NbsPhp\Core\Services\TransactionalApplicationService;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\RequestedDocument\Transformers\BrowseHistoryRequestedDocumentTransformer;
use Sanf\Api\Modules\RequestedDocument\Transformers\BrowseRequestedDocumentTransformer;
use Sanf\Core\Modules\RequestedDocument\Dtos\ListRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Dtos\UploadRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Enums\DocumentTypeEnum;
use Sanf\Core\Modules\RequestedDocument\Enums\RequestedDocumentStatusEnum;
use Sanf\Core\Modules\RequestedDocument\Services\BrowseRequestedDocumentService;
use Sanf\Core\Modules\RequestedDocument\Services\BrowseUploadRequestedDocumentService;
use Sanf\Core\Modules\RequestedDocument\Services\UploadRequestedDocumentService;

final class RequestedDocumentByUserController extends RestApiController
{
    //TODO remove after +1 release version
    // TODO move into document domain
    public function getList(
        Guard $auth,
        Request $request,
        $xid,
        TransactionalSessionInterface $transactionalSession,
        BrowseRequestedDocumentService $service
    ) {
        $input = $this->validate($request, [
            'document_type' => ['nullable', Rule::in(DocumentTypeEnum::ALL)],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(RequestedDocumentStatusEnum::ALL)],
        ]);

        $dto = new ListRequestedDocumentDto(
            $input + [
                'user_id' => $auth->id(),
                'profile_xid' => $xid,
            ]
        );

        $transactionalService = new TransactionalApplicationService($service, $transactionalSession);
        $result = $transactionalService->execute($dto);

        return fractal($result->data, BrowseRequestedDocumentTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getHistory(
        string $xid,
        string $request_id,
        string $document_id,
        Guard $auth,
        BrowseUploadRequestedDocumentService $service
    ) {
        $dto = (object)[
            'user_id' => $auth->id(),
            'profile_xid' => $xid,
            'request_id' => $request_id,
            'document_id' => $document_id
        ];

        $result = $service->execute($dto);

        return fractal($result, BrowseHistoryRequestedDocumentTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function postUpload(
        $xid,
        $request_id,
        $document_id,
        Guard $auth,
        Request $request,
        UploadRequestedDocumentService $service
    ) {
        $input = $this->validate($request, [
            'document_name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
        ]);

        $dto = new UploadRequestedDocumentDto(
            $input + [
                'user_id' => $auth->id(),
                'profile_xid' => $xid,
                'request_id' => $request_id,
                'document_id' => $document_id,
            ]
        );

        $service->execute($dto);

        return $this->responseOk();
    }

    public function postSubmit(
        Guard $auth,
        $xid,
        $request_id
    ) {
        $dto = (object)[
            'profile_xid' => $xid,
            'request_id' => $request_id
        ];

        return $this->responseOk();
    }
}
