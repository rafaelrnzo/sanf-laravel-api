<?php

namespace Sanf\Api\Modules\Disbursement\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Disbursement\Enums\SparePartDisbursementStatusEnum;
use Sanf\Core\Modules\Disbursement\Models\SparePartDisbursementModel;

final class SparePartDisbursementTransformer extends TransformerAbstract
{
    public function transform(SparePartDisbursementModel $model)
    {
        $createdAt = $model->created_at ?? null;
        $updatedAt = $model->updated_at ?? null;

        $status = SparePartDisbursementStatusEnum::from($model->status_id);
        $supplier = $model->supplier;

        $partnerProfile = optional($supplier)->partnerProfile;

        return [
            'xid' => $model->xid,
            'disbursement_no' => $model->batch_number,
            'total_amount' => (float) $model->total_valid_invoice_amount,
            'status' => [
                'id' => $model->status_id,
                'name' => $status->getLabel(),
            ],
            'supplier' => [
                'id' => optional($supplier)->BowheerId,
                'name' => optional($supplier)->BowheerName,
                'code' => optional($supplier)->BowheerCode,
                'email' => optional($supplier)->BowheerEmail,
                'type' => optional($partnerProfile)->tipe_supplier,
            ],
            'invoice_count' => $model->valid_invoice_count,
            'plafond_xid' => $model->plafond_no,
            'created_at' => nullable_unix_timestamp($createdAt),
            'updated_at' => nullable_unix_timestamp($updatedAt),
        ];
    }
}
