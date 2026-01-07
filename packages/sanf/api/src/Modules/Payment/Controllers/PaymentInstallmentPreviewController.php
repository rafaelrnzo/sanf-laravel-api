<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Payment\Entities\PaymentPreviewInstallmentEntity;
use Sanf\Core\Modules\Payment\Exceptions\OutstandingPaymentException;
use Sanf\Core\Modules\Payment\Exceptions\PaymentNoInstallmentException;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentCalculationPayload;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\UseCases\BuildPaymentInstallmentCalculationUseCase;
use Sanf\Core\Modules\Payment\UseCases\PaymentPreviewUseCase;
use Sanf\Core\Modules\Payment\UseCases\SubmitPaymentPreviewUseCase;
use Sanf\Core\Modules\Payment\UseCases\ValidatePaymentInstallmentUseCase;

final class PaymentInstallmentPreviewController extends RestApiController
{
    public function create(
        string $xid,
        Request $request,
        ValidatePaymentInstallmentUseCase $validateUseCase,
        SubmitPaymentPreviewUseCase $submitUseCase
    )
    {
        $formData = $this->validate($request, [
            'installments' => ['required', 'array', 'min:1'],
            'installments.*.contract_no' => ['required'],
            'installments.*.due_date' => ['required', 'integer'],
        ]);

        $installments = array_map(fn ($item) => new PaymentInstallmentPayload($item), $formData['installments']);

        $response = $validateUseCase->execute($installments);

        if (!empty($response->invalidInstallments)) {
            $e = new OutstandingPaymentException();
            $e->setData([
                'installments' => array_map(
                    fn (PaymentInstallmentPayload $item) => [
                        'contract_no' => $item->contract_no,
                        'due_date' => $item->due_date,
                    ],
                    $response->invalidInstallments
                ),
            ]);

            throw $e;
        }

        if (empty($response->validInstallments)) {
            throw new PaymentNoInstallmentException();
        }

        $submitUseCase->execute($xid, $response->validInstallments);

        return $this->responseOk();
    }

    public function show(
        string $xid,
        Request $request,
        PaymentPreviewUseCase $previewUseCase,
        BuildPaymentInstallmentCalculationUseCase $calculationUseCase
    )
    {
        $formData = $this->validate($request, [
            'custom_amount' => ['nullable', 'integer'],
            'custom_penalty_amount' => ['nullable', 'integer'],
        ]);

        $customAmount = null;
        $customPenaltyAmount = null;

        if ($formData['custom_amount'] ?? null) {
            $customAmount = (float) $formData['custom_amount'];
        }

        if ($formData['custom_penalty_amount'] ?? null) {
            $customPenaltyAmount = (float) $formData['custom_penalty_amount'];
        }

        $paymentPreview = $previewUseCase->find($xid);
        $installments = array_map(
            fn (PaymentPreviewInstallmentEntity $item) => new PaymentInstallmentPayload([
                'contract_no' => $item->contract_no,
                'due_date' => Carbon::make($item->due_date)->timestamp,
            ]),
            optional($paymentPreview)->installments ?? []
        );

        $response = $calculationUseCase->execute(new PaymentInstallmentCalculationPayload([
            'profileXid' => $xid,
            'customAmount' => $customAmount,
            'customPenaltyAmount' => $customPenaltyAmount,
            'installments' => $installments,
            'preferCache' => true,
        ]));

        return $this->responseOk('Success', $response->toArray());
    }
}
