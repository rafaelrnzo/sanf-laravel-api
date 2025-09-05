<?php

namespace Sanf\External\Modules\PdcHold\Transformers;

use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\PdcHold\Transformers\PdcHoldGiroSubmissionTransformer;
use Sanf\Api\Modules\PdcHold\Transformers\PdcHoldSubmissionStatusTransformer;
use Sanf\Api\Modules\PdcHold\Transformers\PdcHoldSubmissionTypeTransformer;
use Spatie\Fractalistic\ArraySerializer;

/**
 * @since CR2025
 */
final class WebhookPdcHoldSubmissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'status' => fractal($dto->status, new PdcHoldSubmissionStatusTransformer()),
            'type' => fractal($dto->type, new PdcHoldSubmissionTypeTransformer()),
            'date_start' => Carbon::make($dto->dateStart)->format('Y-m-d'),
            'date_end' => $dto->dateEnd ? Carbon::make($dto->dateEnd)->format('Y-m-d') : null,
            'reason' => $dto->reasonValue,
            'giros' => fractal($dto->giros, new PdcHoldGiroSubmissionTransformer)->serializeWith(new ArraySerializer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}
