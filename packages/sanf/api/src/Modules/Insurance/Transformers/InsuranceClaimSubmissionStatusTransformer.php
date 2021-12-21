<?php

namespace Sanf\Api\Modules\Insurance\Transformers;

use League\Fractal\TransformerAbstract;

final class InsuranceClaimSubmissionStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
