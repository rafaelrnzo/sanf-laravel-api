<?php

namespace Sanf\Api\Modules\PdcHold\Transformers;

use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;

/**
 * @since CR2025
 */
final class MyPdcHoldSubmissionSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'status' => fractal($dto->status, new PdcHoldSubmissionStatusTransformer()),
            'type' => fractal($dto->type, new PdcHoldSubmissionTypeTransformer()),
            'date_start' => Carbon::make($dto->dateStart)->format('Y-m-d'),
            'date_end' => $dto->dateEnd ? Carbon::make($dto->dateEnd)->format('Y-m-d') : null,
            'giro_count' => $dto->type->getValue() == PdcHoldTypeEnum::RESUME ? $dto->resumeGirosCount : $dto->giroNoResumeCount,
            'contract_count' => $dto->type->getValue() == PdcHoldTypeEnum::RESUME ? $dto->contractResumeCount : $dto->contractHoldCount,
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}
