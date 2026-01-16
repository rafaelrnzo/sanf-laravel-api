<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentOutstandingSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentDetailTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $paymentDetail = $model->payment_detail;
        $midtransTransaction = $model->midtransTransaction;

        return [
            'xid' => $model->xid,
            'total_payment' => (float) $model->amount,
            'subtotal_all_installment' => (float) $paymentDetail->subtotal_all_installment,
            'admin_fee' => (float) $paymentDetail->admin_fee,
            'discount' => (float) $paymentDetail->discount,
            'custom_amount' => $paymentDetail->custom_amount !== null ? (float) $paymentDetail->custom_amount : null,
            'custom_penalty_amount' => $paymentDetail->custom_penalty_amount !== null ? (float) $paymentDetail->custom_penalty_amount : null,
            'currency' => $model->currency,
            'category' => $model->category,
            'status' => PaymentStatusEnum::from($model->status)->remapShownStatus(),
            'snap_midtrans' => [
                'token' => optional($midtransTransaction)->midtrans_snap_token,
                'redirect_url' => optional($midtransTransaction)->midtrans_snap_redirect_url,
            ],
            'payment_method' => fractal($model, PaymentMethodTransformer::class),
            'installments' => $model->installments->map(function ($item) {
                /** @var PaymentInstallmentSnapshotEntity */
                $snapshot = $item->pivot->installment_snapshot;

                return [
                    'contract_no' => $snapshot->contract_no,
                    'total_amount' => (float) $snapshot->total_amount,
                    'due_date' => unix_timestamp($snapshot->due_date),
                    'penalty_fee' => (float) $snapshot->penalty_fee,
                    'principal_loan' => (float) $snapshot->principal_loan,
                    'interest_amount' => (float) $snapshot->interest_amount,
                    'financing_type_id' => $snapshot->financing_type_id,
                    'financing_type_desc' => $snapshot->financing_type_desc,
                    'outstanding_installments' => array_map(fn (PaymentInstallmentOutstandingSnapshotEntity $outstanding) => [
                        'due_date' => unix_timestamp($outstanding->due_date ?? $outstanding->jatuh_tempo),
                        'total' => (float) ($outstanding->total ?? $outstanding->total_overdue),
                        'principal_loan' => (float) ($outstanding->principal_loan ?? $outstanding->pokok_hutang),
                        'interest_amount' => (float) ($outstanding->interest_amount ?? $outstanding->bunga),
                        'penalty_fee' => (float) ($outstanding->penalty_fee ?? $outstanding->denda),
                    ], $snapshot->outstanding_installments),
                ];
            }),
            'due_date' => nullable_unix_timestamp($model->expired_at),
            'created_at' => nullable_unix_timestamp($model->created_at),
            'updated_at' => nullable_unix_timestamp($model->updated_at),
        ];
    }
}
