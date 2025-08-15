<?php

namespace Sanf\Api\Modules\PdcHold\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * @since CR2025
 */
final class PdcHoldSubmissionStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
