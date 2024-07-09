<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Plafond\Enums\PlafondDisbursementStatusEnum;

final class PlafondDisbursementStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        switch ($dto->status_id) {
            case PlafondDisbursementStatusEnum::APPROVE:
            case PlafondDisbursementStatusEnum::REJECT:
                $statusId = PlafondDisbursementStatusEnum::DONE;
                break;
            case PlafondDisbursementStatusEnum::ON_PROCESS:
            case PlafondDisbursementStatusEnum::REVISION:
            case PlafondDisbursementStatusEnum::DONE:
                $statusId = PlafondDisbursementStatusEnum::ON_PROCESS;
                break;
            case PlafondDisbursementStatusEnum::SUBMIT:
            default:
                $statusId = PlafondDisbursementStatusEnum::SUBMIT;
                break;
        }

        $status = (new PlafondDisbursementStatusEnum($dto->status_id));

        return [
            'id' => $statusId,
            'name' => $status->getLabel(),
        ];
    }
}
