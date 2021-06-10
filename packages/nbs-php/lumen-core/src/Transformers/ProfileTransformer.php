<?php


namespace NbsPhp\Core\Transformers;


use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'full_name' => $item->full_name
        ];
    }
}
