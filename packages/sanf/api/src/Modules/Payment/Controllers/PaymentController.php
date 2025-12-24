<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Payment\Exceptions\OutstandingPaymentException;
use Sanf\Core\Modules\Payment\Exceptions\UnexistsInstallmentException;
use Sanf\Core\Modules\Payment\Payloads\CreateInstallmentPaymentPayload;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentCalculationPayload;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\UseCases\BuildPaymentInstallmentCalculationUseCase;
use Sanf\Core\Modules\Payment\UseCases\CreateInstallmentPaymentUseCase;
use Sanf\Core\Modules\Payment\UseCases\ValidatePaymentInstallmentUseCase;

final class PaymentController extends RestApiController
{
    public function create(
        string $xid,
        Request $request,
        Guard $auth,
        ValidatePaymentInstallmentUseCase $validateUseCase,
        BuildPaymentInstallmentCalculationUseCase $paymentCalculationUseCase,
        CreateInstallmentPaymentUseCase $createPaymentUseCase
    )
    {
        $formData = $this->validate($request, [
            'installments' => ['required', 'array', 'min:1'],
            'installments.*.contract_no' => ['required'],
            'installments.*.due_date' => ['required', 'integer'],
            'custom_amount' => ['nullable', 'integer'],
            'custom_penalty_amount' => ['nullable', 'integer'],
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

        if (!empty($response->unexistsInstallments)) {
            $e = new UnexistsInstallmentException();
            $e->setData([
                'installments' => array_map(
                    fn (PaymentInstallmentPayload $item) => [
                        'contract_no' => $item->contract_no,
                        'due_date' => $item->due_date,
                    ],
                    $response->unexistsInstallments
                ),
            ]);

            throw $e;
        }

        $customAmount = null;
        $customPenaltyAmount = null;

        if ($formData['custom_amount'] ?? null) {
            $customAmount = (float) $formData['custom_amount'];
        }

        if ($formData['custom_penalty_amount'] ?? null) {
            $customPenaltyAmount = (float) $formData['custom_penalty_amount'];
        }

        $calculationPayload = new PaymentInstallmentCalculationPayload([
            'profileXid' => $xid,
            'customAmount' => $customAmount,
            'customPenaltyAmount' => $customPenaltyAmount,
            'installments' => $installments,
        ]);

        $calclulationResponse = $paymentCalculationUseCase->execute($calculationPayload);

        $createPaymentPayload = new CreateInstallmentPaymentPayload(array_merge($calclulationResponse->toArray(), [
            'userAuthId' => $auth->id(),
            'userProfileXid' => $xid,
        ]));

        $payment = DB::transaction(function () use ($createPaymentUseCase, $createPaymentPayload) {
            return $createPaymentUseCase->execute($createPaymentPayload);
        });

        return $this->responseOk('Success', $payment);
    }
}
