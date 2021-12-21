<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;

final class MyInsuranceClaimSubmissionSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
//            'serial_no' => $dto->serialNo,
//            'polis_no' => $dto->polisNo,
//            'brand_type_model' => $dto->brandTypeModel,
            'status' => fractal($dto->status, new InsuranceClaimSubmissionStatusTransformer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt)
        ];
    }
}
