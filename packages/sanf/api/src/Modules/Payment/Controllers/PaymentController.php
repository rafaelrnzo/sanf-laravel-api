<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Exceptions\ResourceNotFoundException;
use NbsPhp\Core\Transformers\LazyPaginatorAdapter;
use Sanf\Api\Modules\Payment\Transformers\PaymentDetailTransformer;
use Sanf\Api\Modules\Payment\Transformers\PaymentListTransformer;
use Sanf\Api\Modules\Payment\Transformers\PaymentStatsTransformer;
use Sanf\Api\Modules\Payment\Transformers\RegeneratePaymentTransformer;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Payloads\BrowsePaymentPayload;
use Sanf\Core\Modules\Payment\UseCases\BrowsePaymentUseCase;
use Sanf\Core\Modules\Payment\UseCases\CancelPaymentUseCase;
use Sanf\Core\Modules\Payment\UseCases\CheckPaymentStatusUseCase;
use Sanf\Core\Modules\Payment\UseCases\FindPaymentUseCase;
use Sanf\Core\Modules\Payment\UseCases\GetPaymentStatusCountUseCase;
use Sanf\Core\Modules\Payment\UseCases\RegeneratePaymentUseCase;

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

    public function detail(
        string $xid,
        string $paymentXid,
        Guard $auth,
        FindPaymentUseCase $useCase
    )
    {
        $userAuthId = $auth->id();
        $userProfileXid = $xid;

        $response = $useCase->execute($paymentXid, $userAuthId, $userProfileXid);

        if ($response === null) {
            throw new ResourceNotFoundException();
        }

        return fractal($response, PaymentDetailTransformer::class);
    }

    public function checkStatus(
        string $xid,
        string $paymentXid,
        Guard $auth,
        CheckPaymentStatusUseCase $useCase
    )
    {
        $userAuthId = $auth->id();
        $userProfileXid = $xid;

        $status = DB::transaction(fn () => $useCase->execute($paymentXid, $userAuthId, $userProfileXid));

        if ($status === null) {
            throw new ResourceNotFoundException();
        }

        return $this->responseOk('Success', [
            'status' => $status,
        ]);
    }

    public function regenerate(
        string $xid,
        string $paymentXid,
        Guard $auth,
        RegeneratePaymentUseCase $useCase
    )
    {
        $userAuthId = $auth->id();
        $userProfileXid = $xid;

        $payment = DB::transaction(fn () => $useCase->execute($paymentXid, $userAuthId, $userProfileXid));

        if (!$payment) {
            throw new ResourceNotFoundException();
        }

        return fractal($payment, RegeneratePaymentTransformer::class);
    }

    public function cancel(
        string $xid,
        string $paymentXid,
        Guard $auth,
        CancelPaymentUseCase $useCase
    )
    {
        $userAuthId = $auth->id();
        $userProfileXid = $xid;

        $payment = DB::transaction(fn () => $useCase->execute($paymentXid, $userAuthId, $userProfileXid));

        if ($payment === null) {
            throw new ResourceNotFoundException();
        }

        return $this->responseOk();
    }

    /**
     * Static page for SNAP Midtrans redirect when payment success
     * @return \Illuminate\Http\JsonResponse
     */
    public function staticSuccess()
    {
        return $this->responseOk('Payment Success');
    }

    /**
     * Static page for SNAP Midtrans redirect when payment failed
     * @return \Illuminate\Http\JsonResponse
     */
    public function staticFailed()
    {
        return $this->responseOk('Payment Failed');
    }
}
