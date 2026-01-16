<?php

namespace Sanf\Api\Modules\Payment\Transformers;

use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentOutstandingSnapshotEntity;
use Sanf\Core\Modules\Payment\Entities\PaymentInstallmentSnapshotEntity;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentListTransformer extends TransformerAbstract
{
    public function transform(PaymentModel $model)
    {
        $midtransTransaction = $model->midtransTransaction;
        $installments = $model->installments;

        return [
            'xid' => $model->xid,
            'total_amount' => (float) $model->amount,
            'category' => $model->category,
            'status' => PaymentStatusEnum::from($model->status)->remapShownStatus(),
            'payment_method' => fractal($model, PaymentMethodTransformer::class),
            'snap_midtrans' => [
                'token' => optional($midtransTransaction)->midtrans_snap_token,
                'redirect_url' => optional($midtransTransaction)->midtrans_snap_redirect_url,
            ],
            'currency' => $model->currency,
            'contract_nums' => $installments->pluck('contract_no')->toArray(),
            'installment_due_dates' => $this->mapInstallmentDueDates($installments),
            'due_date' => nullable_unix_timestamp($model->expired_at),
            'created_at' => nullable_unix_timestamp($model->created_at),
            'updated_at' => nullable_unix_timestamp($model->updated_at),
        ];
    }

    private function mapInstallmentDueDates($installments)
    {
        $dueDates = [];

        foreach ($installments as $installment) {
            /** @var PaymentInstallmentSnapshotEntity */
            $snapshot = $installment->pivot->installment_snapshot;

            array_push($dueDates, $snapshot->due_date);

            $dueDates = array_merge(
                $dueDates,
                array_map(fn (PaymentInstallmentOutstandingSnapshotEntity $item) => ($item->due_date ?? $item->jatuh_tempo), $snapshot->outstanding_installments ?? [])
            );
        }

        // remove unique & null value
        $dueDates = array_unique(array_filter($dueDates));

        $timestamps = array_map(
            fn (string $item) => Carbon::parse($item)->timestamp,
            $dueDates
        );

        sort($timestamps, SORT_NUMERIC);

        return $timestamps;
    }
}
