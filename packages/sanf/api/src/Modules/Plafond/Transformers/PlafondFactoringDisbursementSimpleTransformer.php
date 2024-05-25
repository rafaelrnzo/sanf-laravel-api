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

        $createdAt = $dto->disbursement_relation->created_at ?? null;
        $updatedAt = $dto->disbursement_relation->updated_at ?? null;

        return [
            'xid' => $dto->xid,
            'disbursement_no' => $dto->disbursement_no,
            'total_amount' => (float) $totalAmount,
            'status_id' => $dto->status_id,
            'status' => $dto->status,
            'notes' => $dto->disbursement_relation->revision_notes,
            'created_at' => ($createdAt) ? unix_timestamp($createdAt) : $createdAt,
            'updated_at' => ($updatedAt) ? unix_timestamp($updatedAt) : $updatedAt,
        ];
    }
}
