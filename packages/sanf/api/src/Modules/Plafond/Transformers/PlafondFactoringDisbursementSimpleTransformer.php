<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondFactoringDisbursementSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        $totalAmount = 0;
        if ($dto->client_amount > 0) {
            $totalAmount = $dto->client_amount;
        }
        if ($dto->customer_amount > 0) {
            $totalAmount = $dto->customer_amount;
        }
        if ($dto->admin_amount > 0) {
            $totalAmount = $dto->admin_amount;
        }

        return [
            'xid' => $dto->xid,
            'disbursement_no' => $dto->disbursement_no,
            'total_amount' => (float) $totalAmount,
            'status_id' => $dto->status_id,
            'status' => $dto->status,
            'created_at' => unix_timestamp($dto->created_at),
            'updated_at' => unix_timestamp($dto->updated_at),
        ];
    }
}
