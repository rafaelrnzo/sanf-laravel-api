<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class InvoicePlafondScanOcrDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'invoice_no' => $dto->invoiceNo,
            'invoce_date' => $dto->invoiceDate,
            'invoice_amount' => $dto->invoiceAmount,
            'tax_amount' => $dto->taxAmount,
            'vat_amount' => $dto->vatAmount,
            'total_amount' => $dto->totalAmount,
        ];
    }
}
