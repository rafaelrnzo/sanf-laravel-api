<?php

namespace Sanf\Api\Modules\Disbursement\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Disbursement\Transformers\SparePartDisbursementTransformer;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Payloads\BrowseSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\UseCases\BrowseSparePartDisbursementUseCase;

class SparePartDisbursementController extends RestApiController
{
    public function list(
        string $xid,
        Request $request,
        BrowseSparePartDisbursementUseCase $useCase
    ) {
        $formData = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'plafond_xid' => ['nullable', 'string'],
            'status_id' => [
                'nullable',
                'integer',
                Rule::in([
                    SparePartDisbursementStatusEnum::WAITING_VALIDATION,
                    SparePartDisbursementStatusEnum::PAYMENT_COMPLETED,
                    SparePartDisbursementStatusEnum::REJECTED,
                    SparePartDisbursementStatusEnum::CANCELED,
                ])
            ],
        ]);

        $payload = new BrowseSparePartDisbursementPayload(array_merge($formData, [
            'profile_xid' => $xid,
            'list_type' => 'HISTORICAL',
        ]));

        $response = $useCase->execute($payload);

        return fractal($response->data)
            ->transformWith(SparePartDisbursementTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($response->paginate));
    }

    public function pendingList(
        string $xid,
        Request $request,
        BrowseSparePartDisbursementUseCase $useCase
    ) {
        $formData = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
            'plafond_xid' => ['nullable', 'string'],
            'status_id' => [
                'nullable',
                'integer',
                Rule::in([
                    SparePartDisbursementStatusEnum::WAITING_CUSTOMER,
                    SparePartDisbursementStatusEnum::NEED_REVIEW,
                ])
            ],
        ]);

        $payload = new BrowseSparePartDisbursementPayload(array_merge($formData, [
            'profile_xid' => $xid,
            'list_type' => 'NEED_APPROVAL',
        ]));

        $response = $useCase->execute($payload);

        return fractal($response->data)
            ->transformWith(SparePartDisbursementTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($response->paginate));
    }
}
