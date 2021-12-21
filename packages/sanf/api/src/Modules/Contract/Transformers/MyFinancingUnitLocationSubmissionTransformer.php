<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

final class MyFinancingUnitLocationSubmissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'status' => fractal($dto->status, new FinancingUnitLocationSubmissionStatusTransformer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt)
        ];
    }
}
