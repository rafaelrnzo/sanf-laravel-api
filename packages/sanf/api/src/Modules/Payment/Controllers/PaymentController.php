<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Payment\Transformers\PaymentListTransformer;
use Sanf\Api\Modules\Payment\Transformers\PaymentStatsTransformer;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Payloads\BrowsePaymentPayload;
use Sanf\Core\Modules\Payment\UseCases\BrowsePaymentUseCase;
use Sanf\Core\Modules\Payment\UseCases\GetPaymentStatusCountUseCase;

final class PaymentController extends RestApiController
{
    public function stats(
        string $xid,
        Guard $auth,
        GetPaymentStatusCountUseCase $useCase
    )
    {
        $userId = $auth->id();

        $response = $useCase->execute($userId, $xid);

        return fractal($response)->transformWith(PaymentStatsTransformer::class);
    }

    public function list(
        string $xid,
        Guard $auth,
        Request $request,
        BrowsePaymentUseCase $useCase
    )
    {
        $formData = $this->validate($request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'contract_no' => ['nullable', 'string'],
            'status' => [
                'nullable',
                'string',
                Rule::in(PaymentStatusEnum::values()),
            ],
        ]);

        $userId = $auth->id();

        $payload = new BrowsePaymentPayload(array_merge($formData, [
            'user_auth_id' => $userId,
            'user_profile_xid' => $xid,
        ]));

        $response = $useCase->execute($payload);

        return fractal($response->data)
            ->transformWith(PaymentListTransformer::class)
            ->paginateWith(new LazyPaginatorAdapter($response->paginate));
    }
}
