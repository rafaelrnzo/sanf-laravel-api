<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondDisbursementInvoiceTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'photos' => fractal($dto->photos_relation)
                ->transformWith(PlafondDisbursementFileMetadataTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'url' => file_get_temp_url($dto->path),
            'file_name' => $dto->file_name,
            'origin_name' => $dto->origin_name,
            'invoice_no' => $dto->document_no,
            'invoice_date' => $dto->document_date,
            'invoice_amount' => (float) $dto->invoice_amount,
            'tax_amount' => (float) $dto->tax_amount,
            'vat_amount' => (float) $dto->vat_amount,
            'backharge_amount' => (float) $dto->backharge_amount,
            'other_amount' => (float) $dto->other_amount,
            'total_amount' => (float) $dto->total_amount,
            'order_no' => $dto->order_no,
        ];
    }
}
