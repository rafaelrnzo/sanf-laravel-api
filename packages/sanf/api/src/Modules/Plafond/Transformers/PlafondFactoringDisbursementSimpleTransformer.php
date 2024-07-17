<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;

final class PlafondFactoringDisbursementSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        $createdAt = $dto->disbursement_relation->created_at ?? null;
        $updatedAt = $dto->disbursement_relation->updated_at ?? null;

        switch ($dto->status_id) {
            case PlafondDisbursementStatusEnum::APPROVE:
            case PlafondDisbursementStatusEnum::REJECT:
                $statusId = PlafondDisbursementStatusEnum::DONE;
                break;
            case PlafondDisbursementStatusEnum::ON_PROCESS:
            case PlafondDisbursementStatusEnum::REVISION:
                $statusId = PlafondDisbursementStatusEnum::ON_PROCESS;
                break;
            case PlafondDisbursementStatusEnum::SUBMIT:
            case PlafondDisbursementStatusEnum::DONE:
            default:
                $statusId = PlafondDisbursementStatusEnum::SUBMIT;
                break;
        }

        $totalAmount = 0;
        foreach ($dto->disbursement_relation->invoices_relation as $invoice) {
            $totalInvoiceAmount = ($invoice->invoice_amount + $invoice->vat_amount + $invoice->other_amount) - ($invoice->tax_amount + $invoice->backharge_amount);
            $totalAmount += $totalInvoiceAmount;
        }

        $status = (new PlafondDisbursementStatusEnum($dto->status_id));

        return [
            'xid' => $dto->xid,
            'disbursement_no' => $dto->disbursement_no,
            'total_amount' => (float) $totalAmount,
            'status_id' => $statusId,
            'status' => $status->getLabel(),
            'notes' => $dto->disbursement_relation->revision_notes,
            'created_at' => ($createdAt) ? unix_timestamp($createdAt) : $createdAt,
            'updated_at' => ($updatedAt) ? unix_timestamp($updatedAt) : $updatedAt,
        ];
    }
}
