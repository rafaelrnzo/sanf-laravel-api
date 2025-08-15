<?php

namespace Sanf\Api\Modules\PdcHold\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * @since CR2025
 */
class PdcHoldReasonListTransformer extends TransformerAbstract
{
    public function transform(object $item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'has_free_text' => $item->has_free_text,
            'order' => $item->order,
        ];
    }
}
