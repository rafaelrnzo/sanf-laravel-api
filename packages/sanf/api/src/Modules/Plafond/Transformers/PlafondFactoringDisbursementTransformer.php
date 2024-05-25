<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondFactoringDisbursementTransformer extends TransformerAbstract
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
            'bouwheer' => fractal($dto)
                ->transformWith(BowheerTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'total_amount' => $totalAmount,
            'status' => fractal($dto)
                ->transformWith(PlafondDisbursementStatusTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'invoices' => fractal($dto->disbursement_relation->invoices_relation)
                ->transformWith(PlafondDisbursementInvoiceTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'allocations' => fractal($dto->disbursement_relation->allocations_relation)
                ->transformWith(PlafondDisbursementAllocationTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'payment_acc_document' => (object) [],
            'other_document' => fractal($dto->disbursement_relation->documents_relation)
                ->transformWith(PlafondDisbursementFileMetadataTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'notes' => $dto->disbursement_relation->revision_notes,
            'created_at' => unix_timestamp($dto->created_at),
            'updated_at' => unix_timestamp($dto->updated_at),
        ];
    }
}
