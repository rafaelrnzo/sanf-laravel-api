<?php

namespace Sanf\Api\Modules\Payment\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Payment\Exceptions\OutstandingPaymentException;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\UseCases\SubmitPaymentPreviewUseCase;
use Sanf\Core\Modules\Payment\UseCases\ValidatePaymentInstallmentUseCase;

final class PaymentPreviewController extends RestApiController
{
    public function create(
        string $xid,
        Request $request,
        ValidatePaymentInstallmentUseCase $validateUseCase,
        SubmitPaymentPreviewUseCase $submitUseCase
    )
    {
        $formData = $this->validate($request, [
            'installments' => ['array', 'min:1'],
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

        $submitUseCase->execute($xid, $response->validInstallments);

        return $this->responseOk();
    }
}
