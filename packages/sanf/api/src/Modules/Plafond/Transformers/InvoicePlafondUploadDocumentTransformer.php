<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class InvoicePlafondUploadDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'url' => file_get_temp_url($dto->path ?? null),
            'file_name' => $dto->fileName,
            'origin_name' => $dto->originName ?? $dto->fileName,
            'invoice_no' => $dto->invoiceNo,
            'invoce_date' => $dto->invoiceDate,
            'invoice_amount' => $dto->invoiceAmount,
            'tax_amount' => $dto->taxAmount,
            'vat_amount' => $dto->vatAmount,
            'total_amount' => $dto->totalAmount,
        ];
    }
}
