<?php

namespace Sanf\Api\Modules\Disbursement\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementInvoiceModel;

final class SparePartDisbursementInvoiceDetailTransformer extends TransformerAbstract
{
    public function transform(SparePartDisbursementInvoiceModel $model)
    {
        $status = SparePartDisbursementStatusEnum::from($model->status_id);

        return [
            'xid' => $model->xid,
            'invoice_no' => $model->invoice_number,
            'invoice_date' => unix_timestamp($model->invoice_date),
            'total_amount' => $model->invoice_amount,
            'sanf_amount' => $model->sanf_amount,
            'currency' => config('payment.currency'),
            'doc_number' => data_get($model->detail, 'doc_number'),
            'recap_number' => data_get($model->detail, 'recap_number'),
            'remark' => data_get($model->detail, 'remark'),
            'status_id' => $model->status_id,
            'status_desc' => $status->getLabel(),
            'created_at' => nullable_unix_timestamp($model->created_at),
            'updated_at' => nullable_unix_timestamp($model->updated_at),
        ];
    }
}
