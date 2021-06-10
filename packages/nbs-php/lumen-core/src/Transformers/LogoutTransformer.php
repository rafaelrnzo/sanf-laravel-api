<?php


namespace NbsPhp\Core\Transformers;


use League\Fractal\TransformerAbstract;

class LogoutTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'logout' => $item
        ];
    }
}
