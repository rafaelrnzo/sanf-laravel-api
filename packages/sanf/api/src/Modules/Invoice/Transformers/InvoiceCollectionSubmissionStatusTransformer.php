<?php

namespace Sanf\Api\Modules\Invoice\Transformers;

use League\Fractal\TransformerAbstract;

final class InvoiceCollectionSubmissionStatusTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'id' => $dto->id,
            'name' => $dto->name,
        ];
    }
}
