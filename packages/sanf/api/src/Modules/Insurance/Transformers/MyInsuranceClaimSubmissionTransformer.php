<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;

final class MyInsuranceClaimSubmissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
//            'serial_no' => $dto->serialNo,
//            'polis_no' => $dto->polisNo,
//            'brand_type_model' => $dto->brandTypeModel,
//            'location_metadata' => $dto->locationMetadata,
//            'incident_date' => Carbon::createFromImmutable($dto->incidentDate)->format('Y-m-d'),
//            'description' => $dto->description,
//            'image_files' => $dto->imageFiles,
            'status' => fractal($dto->status, new InsuranceClaimSubmissionStatusTransformer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt)
        ];
    }
}
