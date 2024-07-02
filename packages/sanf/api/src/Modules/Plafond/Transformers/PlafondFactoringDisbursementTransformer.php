<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;
use Spatie\Fractalistic\ArraySerializer;

final class PlafondFactoringDisbursementTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        $paymentAccDocument = null;
        if (is_null($dto->disbursement_relation->payment_acc_doc_path) === false) {
            $paymentAccDocument = (object) [
                'file_name' => $dto->disbursement_relation->payment_acc_doc_file_name,
                'origin_name' => $dto->disbursement_relation->payment_acc_doc_origin_name,
                'path' => file_get_temp_url($dto->disbursement_relation->payment_acc_doc_path),
            ];
        }

        if (is_null($dto->disbursement_relation->payment_acc_web_doc_path) === false && $dto->status_id === PlafondDisbursementStatusEnum::REVISION) {
            $paymentAccDocument = (object) [
                'file_name' => $dto->disbursement_relation->payment_acc_web_doc_file_name,
                'origin_name' => $dto->disbursement_relation->payment_acc_web_doc_origin_name,
                'path' => file_get_temp_url($dto->disbursement_relation->payment_acc_web_doc_path),
            ];
        }

        $totalAmount = 0;
        foreach ($dto->disbursement_relation->invoices_relation as $invoice) {
            $totalInvoiceAmount = ($invoice->invoice_amount + $invoice->vat_amount + $invoice->other_amount) - ($invoice->tax_amount + $invoice->backharge_amount);
            $totalAmount += $totalInvoiceAmount;
        }

        return [
            'bouwheer' => fractal($dto)
                ->transformWith(BowheerTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'total_amount' => (float) $totalAmount,
            'status' => fractal($dto)
                ->transformWith(PlafondDisbursementStatusTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'invoices' => fractal($dto->disbursement_relation->invoices_relation)
                ->transformWith(PlafondDisbursementInvoiceTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'allocations' => fractal($dto->disbursement_relation->allocations_relation)
                ->transformWith(PlafondDisbursementAllocationTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'payment_acc_document' => $paymentAccDocument,
            'other_document' => fractal($dto->disbursement_relation->documents_relation)
                ->transformWith(PlafondDisbursementFileMetadataTransformer::class)
                ->serializeWith(ArraySerializer::class),
            'notes' => $dto->disbursement_relation->revision_notes,
            'customer_review' => $dto->customer_review,
            'created_at' => unix_timestamp($dto->created_at),
            'updated_at' => unix_timestamp($dto->updated_at),
        ];
    }
}
