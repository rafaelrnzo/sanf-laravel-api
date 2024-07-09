<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;

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

        switch ($dto->status_id) {
            case PlafondDisbursementStatusEnum::APPROVE:
            case PlafondDisbursementStatusEnum::REJECT:
                $statusId = PlafondDisbursementStatusEnum::DONE;
                break;
            case PlafondDisbursementStatusEnum::DONE:
            case PlafondDisbursementStatusEnum::ON_PROCESS:
            case PlafondDisbursementStatusEnum::REVISION:
                $statusId = PlafondDisbursementStatusEnum::ON_PROCESS;
                break;
            case PlafondDisbursementStatusEnum::SUBMIT:
            default:
                $statusId = PlafondDisbursementStatusEnum::SUBMIT;
                break;
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
