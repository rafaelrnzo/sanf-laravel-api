<?php

namespace Sanf\Api\Modules\Contract\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\ArraySerializer;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Contract\Transformers\BrowseHistoryRequestedDocumentTransformer;
use Sanf\Api\Modules\Contract\Transformers\BrowseRequestedDocumentTransformer;
use Sanf\Core\Modules\Contract\Dto\ListRequestedDocumentDto;
use Sanf\Core\Modules\Contract\Dto\UploadRequestedDocumentDto;
use Sanf\Core\Modules\Contract\Enums\DocumentTypeEnum;

final class UploadDocumentRequestByUserController extends RestApiController
{
    //TODO remove after +1 release version
    // TODO move into document domain
    public function getList(
        Guard $auth,
        Request $request,
        $xid
    ) {
        $input = $this->validate($request, [
            'document_type' => ['nullable', Rule::in(DocumentTypeEnum::ALL)],
            'skip' => ['nullable', 'integer', 'max:2147483647'],
            'limit' => ['nullable', 'integer', 'max:2147483647'],
            'sort_by' => ['nullable', 'in:earliest,latest'],
            'keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $dto = new ListRequestedDocumentDto($input + ['profile_xid' => $xid]);
        $result = (object)[
            'data' => json_decode('[{"request_no":123123,"request_date":"12 March 2023","contract_no":123123,"total_document":4,"total_uploaded_document":0,"documents":[{"id":"gsuhJMtRdE9E-ryfRb-dn","title":"Foto Ktp","is_uploaded":false},{"id":"vxKZo35315kp6koT9ocZK","title":"NPWP Perusahaan","is_uploaded":true}]}]'),
            'paginate' => (object)[
                'total' => 0,
                'count' => 0,
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sort_by,
            ]
        ];

        return fractal($result->data, BrowseRequestedDocumentTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($result->paginate));
    }

    public function getHistory(
        Guard $auth,
        $xid,
        $request_id
    ) {
        $dto = (object)[
            'xid' => $xid,
            'request_id' => $request_id,
        ];
        $result = json_decode('[{"upload_at":"01-01-2023","filename":"ktp_new.jpeg"}]');

        return fractal($result, BrowseHistoryRequestedDocumentTransformer::class)
            ->serializeWith(new ArraySerializer());
    }

    public function postUpload(
        Guard $auth,
        $xid,
        $request_id,
        $document_id,
        Request $request
    ) {
        $input = $this->validate($request, [
            'origin' => 'required|string|max:255',
            'filename' => 'required|string|max:255',
        ]);

        $dto = new UploadRequestedDocumentDto(
            $input + [
                'profile_xid' => $xid,
                'request_id' => $request_id,
                'document_id' => $document_id,
            ]
        );

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
