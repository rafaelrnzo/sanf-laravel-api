<?php


namespace Sanf\Api\Modules\Asset;


use League\Fractal\TransformerAbstract;

class AssetSimpleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'file_url' => file_get_url($item->file_name),
            'origin_name' => $item->origin_name,
        ];
    }
}
