<?php

namespace Sanf\Api\Modules\Plafond\Transformers;

use League\Fractal\TransformerAbstract;

final class PlafondDisbursementStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->status_id,
            'name' => $dto->status,
        ];
    }
}
