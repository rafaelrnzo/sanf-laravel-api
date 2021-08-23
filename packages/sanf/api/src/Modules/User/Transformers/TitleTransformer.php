<?php


namespace Sanf\Api\Modules\User\Transformers;


use League\Fractal\TransformerAbstract;

class TitleTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
        ];
    }
}
