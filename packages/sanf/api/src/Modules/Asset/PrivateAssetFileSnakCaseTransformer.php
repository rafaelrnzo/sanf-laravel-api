<?php

namespace Sanf\Api\Modules\Asset;

use League\Fractal\TransformerAbstract;

/**
 * @since CR2025 Used to workaround PrivateAssetFileSimpleTransformer refactor that only get from database without uploading back.
 */
class PrivateAssetFileSnakCaseTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'url' => file_get_temp_url($item->path ?? null),
            'file_name' => $item->file_name,
            'origin_name' => $item->origin_name ?? $item->file_name,
        ];
    }
}
