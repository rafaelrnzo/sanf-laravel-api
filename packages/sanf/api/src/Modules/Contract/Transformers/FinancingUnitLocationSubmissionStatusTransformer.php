<?php

namespace Sanf\Api\Modules\Contract\Transformers;

use League\Fractal\TransformerAbstract;

final class FinancingUnitLocationSubmissionStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
