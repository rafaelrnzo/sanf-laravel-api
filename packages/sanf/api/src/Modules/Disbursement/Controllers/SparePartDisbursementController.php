<?php

namespace Sanf\Api\Modules\Disbursement\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Disbursement\Transformers\SparePartDisbursementDetailTransformer;
use Sanf\Api\Modules\Disbursement\Transformers\SparePartDisbursementInvoiceDetailTransformer;
use Sanf\Api\Modules\Disbursement\Transformers\SparePartDisbursementTransformer;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Payloads\BrowseSparePartDisbursementPayload;
use Sanf\Core\Modules\Disbursement\UseCases\BrowseSparePartDisbursementUseCase;
use Sanf\Core\Modules\Disbursement\UseCases\FindSparePartDisbursementInvoiceUseCase;
use Sanf\Core\Modules\Disbursement\UseCases\FindSparePartDisbursementUseCase;

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
                ]),
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
                ]),
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

    public function detail(
        string $xid,
        string $disbursementXid,
        FindSparePartDisbursementUseCase $useCase
    ) {
        $response = $useCase->execute($xid, $disbursementXid);

        if ($response === null) {
            throw new ResourceNotFoundException();
        }

        return fractal($response, new SparePartDisbursementDetailTransformer());
    }

    public function invoiceDetail(
        string $xid,
        string $disbursementXid,
        string $invoiceXid,
        FindSparePartDisbursementInvoiceUseCase $useCase
    ) {
        $response = $useCase->execute($xid, $disbursementXid, $invoiceXid);

        if ($response === null) {
            throw new ResourceNotFoundException();
        }

        return fractal($response, new SparePartDisbursementInvoiceDetailTransformer());
    }
}
