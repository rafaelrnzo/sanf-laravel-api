<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class InvoicePlafondScanOcrDocumentTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'invoice_no' => $dto->invoiceNo,
            'invoice_date' => $dto->invoiceDate,
            'invoice_amount' => $dto->invoiceAmount,
            'tax_amount' => $dto->taxAmount,
            'vat_amount' => $dto->vatAmount,
            'backharge_amount' => $dto->backhargeAmount,
            'other_amount' => $dto->otherAmount,
            'total_amount' => $dto->totalAmount,
        ];
    }
}
