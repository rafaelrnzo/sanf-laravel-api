<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Api\Modules\Asset\AssetFileSimpleTransformer;
use Spatie\Fractalistic\ArraySerializer;

final class MyInsuranceClaimSubmissionTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'serial_no' => $dto->serialNo,
            'polis_no' => $dto->polisNo,
            'brand_type_model' => $dto->brandTypeModel,
            'year' => $dto->year,
            'location_metadata' => $dto->locationMetadata,
            'incident_date' => $dto->incidentDate->format('Y-m-d'),
            'description' => $dto->description,
            'image_files' => fractal($dto->imageFiles, AssetFileSimpleTransformer::class)->serializeWith(new ArraySerializer()),
            'status' => fractal($dto->status, new InsuranceClaimSubmissionStatusTransformer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt)
        ];
    }
}
