<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondDisbursementInvoiceTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        $totalAmount = ($dto->invoice_amount + $dto->vat_amount + $dto->other_amount) - ($dto->tax_amount + $dto->backharge_amount);

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
            'total_amount' => (float) $totalAmount,
            'sales_amount' => $dto->sales_amount ?? (float) $totalAmount,
            'order_no' => $dto->order_no,
            'due_at' => ($dto->due_at) ? Carbon::parse($dto->due_at)->format('Y-m-d') : null,
        ];
    }
}
