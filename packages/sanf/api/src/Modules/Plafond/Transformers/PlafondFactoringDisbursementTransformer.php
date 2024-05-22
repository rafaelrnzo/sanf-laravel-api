<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondFactoringDisbursementTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'bouwheer' => $dto->bouwheer,
            'code' => $dto->code,
            'total_amount' => $dto->total_amount,
            'status' => $dto->status,
            'notes' => $dto->notes,
            'invoices' => $dto->invoices,
            'allocations' => $dto->allocations,
            'payment_acc_document' => $dto->payment_acc_document,
            'other_document' => $dto->other_document,
            'created_at' => $dto->created_at,
            'updated_at' => $dto->updated_at,
        ];
    }
}
